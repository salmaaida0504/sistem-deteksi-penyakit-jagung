<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opt;
use App\Models\Produk;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::with(['jenisProduk', 'opt'])->orderBy('nama_produk')->get();
        return view('admin.produk.index', compact('produks'));
    }

    public function create()
    {
        $opts = Opt::all();
        $jenis_produks = JenisProduk::all();
        return view('admin.produk.create', compact('opts', 'jenis_produks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jenis' => 'required|exists:jenis_produk,id_jenis',
            'nama_produk' => 'required|string|max:300',
            'deskripsi_produk' => 'nullable|string',
            'manfaat' => 'nullable|string|max:500',
            'keunggulan' => 'nullable|string|max:500',
            'dosis_penggunaan' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'opt_ids' => 'required|array|min:1',
            'opt_ids.*' => 'exists:opt,id_opt',
        ]);

        if ($request->hasFile('image')) {
            $validated['foto_produk'] = $request->file('image')->store('produk', 'public');
        }

        $optIds = $validated['opt_ids'];
        unset($validated['opt_ids']);

        $produk = Produk::create($validated);
        $produk->opt()->attach($optIds);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $opts = Opt::all();
        $jenis_produks = JenisProduk::all();
        $selectedOpts = $produk->opt->pluck('id_opt')->toArray();
        return view('admin.produk.edit', compact('produk', 'opts', 'jenis_produks', 'selectedOpts'));
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'id_jenis' => 'required|exists:jenis_produk,id_jenis',
            'nama_produk' => 'required|string|max:300',
            'deskripsi_produk' => 'nullable|string',
            'manfaat' => 'nullable|string|max:500',
            'keunggulan' => 'nullable|string|max:500',
            'dosis_penggunaan' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'opt_ids' => 'required|array|min:1',
            'opt_ids.*' => 'exists:opt,id_opt',
        ]);

        if ($request->hasFile('image')) {
            if ($produk->foto_produk && Storage::disk('public')->exists($produk->foto_produk)) {
                Storage::disk('public')->delete($produk->foto_produk);
            }
            $validated['foto_produk'] = $request->file('image')->store('produk', 'public');
        }

        $optIds = $validated['opt_ids'];
        unset($validated['opt_ids']);

        $produk->update($validated);
        $produk->opt()->sync($optIds);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->foto_produk && Storage::disk('public')->exists($produk->foto_produk)) {
            Storage::disk('public')->delete($produk->foto_produk);
        }

        $produk->opt()->detach();
        $produk->delete();

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.produk.index')
                ->with('success', 'Tidak ada data yang dipilih.');
        }

        $produks = Produk::whereIn('id_produk', $ids)->get();

        foreach ($produks as $produk) {
            if ($produk->foto_produk && Storage::disk('public')->exists($produk->foto_produk)) {
                Storage::disk('public')->delete($produk->foto_produk);
            }
            $produk->opt()->detach();
            $produk->delete();
        }

        $count = $produks->count();

        return redirect()->route('admin.produk.index')
            ->with('success', "{$count} produk berhasil dihapus.");
    }
}
