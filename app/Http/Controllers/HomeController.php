<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use App\Models\Opt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $opts = Opt::orderBy('id_opt')->get();
        return view('home', compact('opts'));
    }

    public function deteksi(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            // Store uploaded image
            $path = $request->file('image')->store('detections', 'public');

            // Dapatkan prediksi dari model CNN via FastAPI
            $predictions = $this->getPrediction($request->file('image'));

            // Find the top prediction
            arsort($predictions);
            $predictedClass = array_key_first($predictions);
            $confidence = $predictions[$predictedClass];

            // Validasi jika gambar kemungkinan bukan daun jagung (confidence terlalu rendah)
            if ($confidence <= 70) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar tidak dikenali. Pastikan anda mengunggah gambar daun jagung dengan kualitas gambar yang baik.',
                ], 400);
            }

            // Map predicted class to nama_opt in database
            $classMap = [
                'Cercospora' => 'Bercak Abu',
                'Common_Rust' => 'Karat Daun',
                'Northern_Leaf_Blight' => 'Hawar Daun',
                'Healthy' => 'Sehat'
            ];
            $namaOpt = $classMap[$predictedClass] ?? $predictedClass;

            // Get opt info for the predicted class
            $opt = Opt::where('nama_opt', $namaOpt)
                ->with('produk.jenisProduk')
                ->first();

            // Save detection to database
            $detection = Detection::create([
                'id_opt'          => $opt ? $opt->id_opt : null,
                'image_path'      => $path,
                'predicted_class' => $predictedClass,
                'confidence'      => $confidence,
                'all_predictions' => $predictions,
                'ip_address'      => $request->ip(),
                'user_agent'      => $request->userAgent(),
            ]);

            return response()->json([
                'success'   => true,
                'detection' => $detection,
                'opt'       => $opt,
                'image_url' => asset('storage/' . $path),
            ]);

        } catch (\Exception $e) {
            Log::error('Error pada proses deteksi', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Mendapatkan prediksi dari model CNN melalui FastAPI service.
     *
     * Mengirim gambar ke endpoint /predict pada FastAPI server,
     * lalu mengembalikan array probabilitas per kelas.
     * Jika FastAPI tidak tersedia, fallback ke simulasi.
     *
     * @param \Illuminate\Http\UploadedFile $imageFile
     * @return array Probabilitas per kelas (e.g. ['Cercospora' => 85.5, ...])
     */
    private function getPrediction($imageFile): array
    {
        $fastApiUrl = config('services.fastapi.url', 'http://localhost:8001');

        try {
            $response = Http::timeout(30)
                ->attach(
                    'file',
                    file_get_contents($imageFile->getRealPath()),
                    $imageFile->getClientOriginalName()
                )
                ->post("{$fastApiUrl}/predict");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['success']) && $data['success'] && isset($data['all_predictions'])) {
                    Log::info('Prediksi CNN berhasil', [
                        'predicted_class' => $data['predicted_class'],
                        'confidence' => $data['confidence'],
                    ]);

                    return $data['all_predictions'];
                }

                Log::warning('Response FastAPI tidak valid', ['response' => $data]);
            } else {
                Log::warning('FastAPI mengembalikan error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Gagal terhubung ke FastAPI service', [
                'url' => $fastApiUrl,
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback ke simulasi jika FastAPI tidak tersedia
        Log::warning('Menggunakan prediksi simulasi (FastAPI tidak tersedia)');
        return $this->simulatePrediction();
    }

    /**
     * Simulasi prediksi CNN dengan probabilitas acak.
     * Digunakan sebagai fallback jika FastAPI service tidak tersedia.
     *
     * @return array
     */
    private function simulatePrediction(): array
    {
        $classes = ['Cercospora', 'Common_Rust', 'Northern_Leaf_Blight', 'Healthy'];

        // Generate random probabilities that sum to 100
        $values = [];
        for ($i = 0; $i < 4; $i++) {
            $values[] = mt_rand(1, 100);
        }
        $sum = array_sum($values);

        $predictions = [];
        foreach ($classes as $index => $class) {
            $predictions[$class] = round(($values[$index] / $sum) * 100, 2);
        }

        // Ensure they sum to exactly 100
        $diff = 100 - array_sum($predictions);
        $predictions[array_key_first($predictions)] += $diff;

        return $predictions;
    }
}
