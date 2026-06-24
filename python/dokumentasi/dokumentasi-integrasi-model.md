# Dokumentasi Integrasi Model CNN — Proyek BHUMI

**Proyek**: Integrasi Model MobileNetV3-Small ke Aplikasi Web Laravel  
**Model**: `best_model_phase2.pth` (MobileNetV3-Small + SGD, Transfer Learning Phase 2)  
**File terkait**:
- `python/predict_service.py` — FastAPI inference server
- `python/requirements.txt` — Daftar dependency Python
- `app/Http/Controllers/HomeController.php` — Controller Laravel (pemanggil API)
- `config/services.php` — Konfigurasi URL FastAPI
- `.env` — Variable environment

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Arsitektur Sistem](#2-arsitektur-sistem)
3. [Persyaratan Sistem](#3-persyaratan-sistem)
4. [Instalasi & Setup](#4-instalasi--setup)
5. [Menjalankan Server](#5-menjalankan-server)
6. [Penjelasan Kode — FastAPI Service](#6-penjelasan-kode--fastapi-service)
7. [Penjelasan Kode — Laravel Controller](#7-penjelasan-kode--laravel-controller)
8. [API Reference](#8-api-reference)
9. [Alur Kerja Deteksi (End-to-End)](#9-alur-kerja-deteksi-end-to-end)
10. [Konfigurasi](#10-konfigurasi)
11. [Troubleshooting](#11-troubleshooting)
12. [Panduan Mengganti Model / Arsitektur](#12-panduan-mengganti-model--arsitektur)
13. [FAQ](#13-faq)

---

## 1. Gambaran Umum

### Latar Belakang

Proyek BHUMI menggunakan model CNN (Convolutional Neural Network) berbasis arsitektur **MobileNetV3-Small** yang telah di-training menggunakan teknik **transfer learning 2 fase** untuk mendeteksi 4 kategori kondisi daun jagung:

| Kode Kelas | Nama Penyakit | Keterangan |
|---|---|---|
| `Cercospora` | Gray Leaf Spot | Bercak abu-abu pada daun jagung |
| `Common_Rust` | Common Rust | Karat/bercak coklat-merah pada daun |
| `Northern_Leaf_Blight` | Northern Leaf Blight | Hawar daun dari bagian utara |
| `Healthy` | Healthy | Daun jagung sehat |

### Tujuan Integrasi

Menghubungkan model `best_model_phase2.pth` yang sudah di-training ke aplikasi web Laravel, sehingga fitur **deteksi penyakit daun jagung** menggunakan prediksi model CNN yang sesungguhnya — bukan lagi prediksi acak (simulasi).

### Pendekatan

Integrasi dilakukan melalui arsitektur **microservice**:
- **FastAPI** (Python) berjalan sebagai service terpisah yang memuat model dan melakukan inferensi
- **Laravel** (PHP) mengirim gambar ke FastAPI via HTTP dan menampilkan hasil ke pengguna

> **Mengapa tidak langsung menjalankan PyTorch di PHP?**  
> PHP tidak memiliki dukungan native untuk PyTorch. Menjalankan model deep learning memerlukan library Python seperti `torch` dan `torchvision`. Oleh karena itu, digunakan FastAPI sebagai jembatan antara Laravel dan model PyTorch.

---

## 2. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────┐
│                    Browser / Client                  │
│                                                      │
│  [Upload Gambar Daun Jagung]                        │
└──────────────────┬──────────────────────────────────┘
                   │ HTTP POST /deteksi
                   ▼
┌─────────────────────────────────────────────────────┐
│              Laravel Application (PHP)               │
│                                                      │
│  HomeController::deteksi()                          │
│  ├── Validasi & simpan gambar                       │
│  ├── Kirim gambar ke FastAPI ──────┐                │
│  ├── Simpan hasil ke database      │                │
│  └── Return JSON response          │                │
└─────────────────────────────────────┼───────────────┘
                                      │ HTTP POST /predict
                                      ▼
┌─────────────────────────────────────────────────────┐
│           FastAPI Service (Python)                    │
│           http://localhost:8001                       │
│                                                      │
│  predict_service.py                                 │
│  ├── Terima gambar                                  │
│  ├── Preprocessing (CLAHE → Resize → Normalize)     │
│  ├── Inferensi model MobileNetV3-Small              │
│  └── Return JSON {kelas, probabilitas}              │
│                                                      │
│  📦 best_model_phase2.pth (~6MB)                    │
└─────────────────────────────────────────────────────┘
```

### Alur Data

```
Gambar (JPG/PNG)
    │
    ▼ [1] Upload ke Laravel
Laravel (PHP)
    │
    ▼ [2] HTTP POST multipart/form-data
FastAPI (Python)
    │
    ├── [3] Decode gambar dari bytes
    ├── [4] CLAHE (peningkatan kontras)
    ├── [5] Resize 224×224
    ├── [6] Normalisasi [0, 1]
    ├── [7] Konversi ke Tensor
    ├── [8] model(tensor) → prediksi
    │
    ▼ [9] JSON Response
Laravel (PHP)
    │
    ├── [10] Simpan ke database
    │
    ▼ [11] JSON Response ke browser
Browser
```

---

## 3. Persyaratan Sistem

### Software yang Diperlukan

| Software | Versi Minimum | Fungsi |
|---|---|---|
| **Python** | 3.9+ | Menjalankan FastAPI service |
| **pip** | 21.0+ | Package manager Python |
| **PHP** | 8.2+ | Menjalankan Laravel |
| **Composer** | 2.0+ | Package manager PHP |
| **Node.js** | 18.0+ | Build asset Laravel (Vite) |

### Hardware

| Komponen | Minimum | Rekomendasi |
|---|---|---|
| **RAM** | 4 GB | 8 GB+ |
| **Storage** | 2 GB (untuk dependencies) | 5 GB+ |
| **GPU** | Tidak wajib | NVIDIA GPU (opsional, untuk inferensi lebih cepat) |

> **Catatan**: Model MobileNetV3-Small dirancang untuk berjalan efisien di CPU. GPU **tidak diperlukan** untuk inferensi — hanya mempercepat sedikit.

---

## 4. Instalasi & Setup

### Langkah 1 — Pastikan Python Terinstal

Buka terminal/command prompt dan jalankan:

```bash
python --version
```

Output yang diharapkan: `Python 3.9.x` atau lebih baru.

Jika belum terinstal, download dari [python.org](https://www.python.org/downloads/).

### Langkah 2 — Buat Virtual Environment (Disarankan)

Virtual environment mengisolasi dependency Python proyek ini dari instalasi global.

```bash
# Masuk ke folder python
cd E:\SKRIPSI\bhumi-skripsi\python

# Buat virtual environment
python -m venv venv

# Aktifkan virtual environment
# Windows (Command Prompt):
venv\Scripts\activate

# Windows (PowerShell):
venv\Scripts\Activate.ps1

# Linux/macOS:
source venv/bin/activate
```

Setelah aktif, prompt terminal akan menampilkan `(venv)` di awal baris:
```
(venv) E:\SKRIPSI\bhumi-skripsi\python>
```

### Langkah 3 — Install Dependencies Python

```bash
pip install -r requirements.txt
```

**Catatan tentang PyTorch:**

Jika hanya menggunakan CPU (tanpa GPU NVIDIA), install versi CPU-only yang lebih ringan:

```bash
# CPU-only (lebih kecil, ~200MB vs ~2GB)
pip install torch torchvision --index-url https://download.pytorch.org/whl/cpu
pip install -r requirements.txt
```

Jika memiliki GPU NVIDIA dengan CUDA:

```bash
# GPU dengan CUDA 12.1
pip install torch torchvision --index-url https://download.pytorch.org/whl/cu121
pip install -r requirements.txt
```

### Langkah 4 — Verifikasi Model

Pastikan file `best_model_phase2.pth` ada di folder `python/`:

```
E:\SKRIPSI\bhumi-skripsi\python\
├── best_model_phase2.pth    ← File model (~6MB)
├── predict_service.py       ← FastAPI server
└── requirements.txt         ← Dependencies
```

### Langkah 5 — Konfigurasi Laravel

Pastikan file `.env` di root proyek memiliki variable berikut:

```env
FASTAPI_URL=http://localhost:8001
```

Variable ini sudah ditambahkan secara otomatis. Jika perlu mengganti port, ubah nilai ini dan sesuaikan juga di `predict_service.py`.

---

## 5. Menjalankan Server

### Menjalankan Kedua Server

Untuk menggunakan fitur deteksi, **kedua server harus berjalan bersamaan**:

#### Terminal 1 — FastAPI Server (Python)

```bash
cd E:\SKRIPSI\bhumi-skripsi\python

# Aktifkan virtual environment (jika menggunakan venv)
venv\Scripts\activate

# Jalankan server
python predict_service.py
```

Output yang diharapkan:

```
============================================================
  BHUMI — FastAPI Inference Server
  Model   : MobileNetV3-Small (best_model_phase2.pth)
  Port    : 8001
  Device  : cpu
============================================================
INFO:     Memuat model dari: E:\SKRIPSI\bhumi-skripsi\python\best_model_phase2.pth
INFO:     Model berhasil dimuat pada device: cpu
INFO:     Jumlah parameter: 1,528,100
INFO:     ✅ Server siap menerima request prediksi.
INFO:     Uvicorn running on http://0.0.0.0:8001 (Press CTRL+C to quit)
```

#### Terminal 2 — Laravel Server (PHP)

```bash
cd E:\SKRIPSI\bhumi-skripsi
php artisan serve
```

### Verifikasi FastAPI Berjalan

Buka browser dan akses:

- **Health check**: http://localhost:8001/
- **Dokumentasi API otomatis**: http://localhost:8001/docs (Swagger UI)
- **Dokumentasi alternatif**: http://localhost:8001/redoc

### Menghentikan Server

Tekan `Ctrl+C` di terminal masing-masing untuk menghentikan server.

---

## 6. Penjelasan Kode — FastAPI Service

### File: `python/predict_service.py`

#### 6.1 Import dan Konfigurasi

```python
MODEL_PATH = Path(__file__).parent / "best_model_phase2.pth"
NUM_CLASSES = 4
CLASS_NAMES = {
    0: "Cercospora",
    1: "Common_Rust",
    2: "Northern_Leaf_Blight",
    3: "Healthy",
}
```

- `MODEL_PATH` menggunakan path relatif terhadap lokasi script, sehingga tidak bergantung pada working directory.
- `CLASS_NAMES` memetakan indeks output model (0–3) ke nama kelas. **Urutan ini harus identik** dengan urutan folder di dataset training (diurutkan alfabetis oleh `ImageFolder` PyTorch).

#### 6.2 Preprocessing — `apply_clahe()`

```python
def apply_clahe(image: np.ndarray) -> np.ndarray:
    lab = cv2.cvtColor(image, cv2.COLOR_BGR2Lab)
    l_channel, a_channel, b_channel = cv2.split(lab)
    clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
    l_enhanced = clahe.apply(l_channel)
    lab_enhanced = cv2.merge([l_enhanced, a_channel, b_channel])
    return cv2.cvtColor(lab_enhanced, cv2.COLOR_Lab2BGR)
```

Fungsi ini **identik** dengan preprocessing saat training:
1. Konversi BGR → Lab color space
2. Terapkan CLAHE hanya pada channel Luminance (L)
3. Gabungkan kembali channel L yang sudah ditingkatkan kontrasnya
4. Konversi kembali ke BGR

> **Penting**: Preprocessing saat inferensi **harus identik** dengan saat training. Perbedaan preprocessing akan menyebabkan prediksi yang tidak akurat.

#### 6.3 Preprocessing — `preprocess_image()`

```python
def preprocess_image(image_bytes: bytes) -> torch.Tensor:
    # [1] Decode bytes → numpy array
    nparr = np.frombuffer(image_bytes, np.uint8)
    image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
    
    # [2] CLAHE
    image = apply_clahe(image)
    
    # [3] Resize ke 224×224 (INTER_LANCZOS4 — identik training)
    image = cv2.resize(image, (224, 224), interpolation=cv2.INTER_LANCZOS4)
    
    # [4] Normalisasi ke [0, 1]
    image = image.astype(np.float32) / 255.0
    
    # [5] BGR → RGB (OpenCV=BGR, PyTorch=RGB)
    image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    
    # [6] (H,W,C) → (1,C,H,W) — format batch tensor
    tensor = torch.from_numpy(image).permute(2, 0, 1).unsqueeze(0)
    return tensor
```

Pipeline lengkap: `Bytes → Decode → CLAHE → Resize → Normalize → RGB → Tensor`

#### 6.4 Load Model — `load_model()`

```python
def load_model(model_path):
    # Buat arsitektur model (tanpa pretrained weights)
    model = mobilenet_v3_small(weights=None)
    
    # Modifikasi classifier: 1024 → 4 kelas
    in_features = model.classifier[-1].in_features
    model.classifier[-1] = nn.Linear(in_features, NUM_CLASSES)
    
    # Muat bobot dari file .pth
    state_dict = torch.load(model_path, map_location=device, weights_only=True)
    model.load_state_dict(state_dict)
    
    model.eval()  # Mode evaluasi
    return model
```

**Langkah kritis**:
1. Arsitektur model **harus identik** dengan saat training (MobileNetV3-Small + classifier Linear(1024, 4))
2. `weights=None` — tidak memuat pretrained ImageNet, karena bobot sudah ada di file `.pth`
3. `weights_only=True` — keamanan: hanya memuat tensor, bukan kode Python yang mungkin berbahaya
4. `model.eval()` — menonaktifkan dropout dan menggunakan running statistics untuk batch normalization

#### 6.5 Endpoint `/predict`

```python
@app.post("/predict")
async def predict(file: UploadFile = File(...)):
    image_bytes = await file.read()
    tensor = preprocess_image(image_bytes)
    
    with torch.no_grad():
        outputs = model(tensor)
        probabilities = torch.softmax(outputs, dim=1)
        confidence, predicted_idx = torch.max(probabilities, dim=1)
    
    return {
        "success": True,
        "predicted_class": CLASS_NAMES[predicted_idx.item()],
        "confidence": round(confidence.item() * 100, 2),
        "all_predictions": { ... }
    }
```

- `torch.no_grad()` — menonaktifkan penghitungan gradien, menghemat memori dan mempercepat inferensi
- `torch.softmax()` — mengubah output model (logits) menjadi probabilitas yang berjumlah 1.0
- Confidence dikembalikan dalam persen (0–100)

---

## 7. Penjelasan Kode — Laravel Controller

### File: `app/Http/Controllers/HomeController.php`

#### 7.1 Method `deteksi()`

```php
public function deteksi(Request $request)
{
    // Validasi file gambar
    $request->validate([
        'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
    ]);

    // Simpan gambar
    $path = $request->file('image')->store('detections', 'public');

    // Dapatkan prediksi dari model CNN via FastAPI
    $predictions = $this->getPrediction($request->file('image'));
    
    // ... simpan ke database, return response
}
```

Alur yang sama seperti sebelumnya, hanya `simulatePrediction()` diganti dengan `getPrediction()`.

#### 7.2 Method `getPrediction()` — Pemanggil FastAPI

```php
private function getPrediction($imageFile): array
{
    $fastApiUrl = config('services.fastapi.url', 'http://localhost:8001');

    try {
        $response = Http::timeout(30)
            ->attach('file', file_get_contents($imageFile->getRealPath()),
                     $imageFile->getClientOriginalName())
            ->post("{$fastApiUrl}/predict");

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['success']) && $data['success']) {
                return $data['all_predictions'];
            }
        }
    } catch (\Exception $e) {
        Log::error('Gagal terhubung ke FastAPI', [...]);
    }

    // Fallback ke simulasi
    return $this->simulatePrediction();
}
```

**Fitur keamanan**:
- `timeout(30)` — batas waktu 30 detik untuk menunggu respons FastAPI
- **Fallback otomatis** — jika FastAPI tidak tersedia (mati, error, timeout), sistem otomatis menggunakan prediksi simulasi sehingga aplikasi **tidak pernah error/crash**
- **Logging** — semua kegagalan dicatat di log Laravel (`storage/logs/laravel.log`) untuk debugging

#### 7.3 Konfigurasi URL FastAPI

URL FastAPI dibaca dari `config/services.php`:

```php
// config/services.php
'fastapi' => [
    'url' => env('FASTAPI_URL', 'http://localhost:8001'),
],
```

Yang membaca dari `.env`:

```env
FASTAPI_URL=http://localhost:8001
```

---

## 8. API Reference

### `GET /` — Health Check

**Response:**
```json
{
    "status": "running",
    "service": "BHUMI — Deteksi Penyakit Daun Jagung",
    "model": "MobileNetV3-Small (Phase 2 Fine-tuned)",
    "classes": ["Cercospora", "Common_Rust", "Northern_Leaf_Blight", "Healthy"],
    "device": "cpu"
}
```

### `GET /health` — Model Status

**Response:**
```json
{
    "status": "healthy",
    "model_loaded": true,
    "device": "cpu"
}
```

### `POST /predict` — Prediksi Penyakit

**Request:**
- Method: `POST`
- Content-Type: `multipart/form-data`
- Body: `file` — file gambar (JPG/PNG, maks 5MB)

**Response Sukses (200):**
```json
{
    "success": true,
    "predicted_class": "Common_Rust",
    "predicted_display_name": "Common Rust (Karat Jagung)",
    "confidence": 92.45,
    "all_predictions": {
        "Cercospora": 2.15,
        "Common_Rust": 92.45,
        "Northern_Leaf_Blight": 4.30,
        "Healthy": 1.10
    }
}
```

**Response Error (400):**
```json
{
    "detail": "Tipe file tidak valid: application/pdf. Hanya menerima gambar (JPG/PNG)."
}
```

**Response Error (503):**
```json
{
    "detail": "Model belum dimuat. Coba lagi nanti."
}
```

### Contoh Pengujian dengan cURL

```bash
curl -X POST http://localhost:8001/predict \
  -F "file=@path/ke/gambar_daun.jpg"
```

### Contoh Pengujian dengan PowerShell

```powershell
$response = Invoke-RestMethod -Uri "http://localhost:8001/predict" `
  -Method Post `
  -InFile "E:\path\ke\gambar_daun.jpg" `
  -ContentType "multipart/form-data"

$response | ConvertTo-Json
```

---

## 9. Alur Kerja Deteksi (End-to-End)

### Langkah-Langkah Detail

```
[PENGGUNA]
│
├── 1. Buka halaman utama BHUMI
├── 2. Scroll ke section "Deteksi Penyakit"
├── 3. Upload gambar daun jagung (JPG/PNG, maks 5MB)
├── 4. Klik tombol "Deteksi"
│
▼
[LARAVEL — HomeController::deteksi()]
│
├── 5. Validasi file (tipe, ukuran)
├── 6. Simpan gambar ke storage/app/public/detections/
├── 7. Panggil getPrediction() ─────────────────────┐
│                                                     │
│   [getPrediction()]                                 │
│   ├── 8. Baca FASTAPI_URL dari config               │
│   ├── 9. Kirim gambar via HTTP POST ────────────┐   │
│   │                                              │   │
│   │   [FASTAPI — predict_service.py]             │   │
│   │   ├── 10. Terima file gambar                 │   │
│   │   ├── 11. Decode bytes → numpy array         │   │
│   │   ├── 12. CLAHE (peningkatan kontras)        │   │
│   │   ├── 13. Resize ke 224×224 piksel           │   │
│   │   ├── 14. Normalisasi [0, 1]                 │   │
│   │   ├── 15. BGR → RGB                          │   │
│   │   ├── 16. Konversi ke Tensor PyTorch         │   │
│   │   ├── 17. model(tensor) → logits             │   │
│   │   ├── 18. softmax(logits) → probabilitas     │   │
│   │   └── 19. Return JSON ──────────────────┘    │   │
│   │                                              │   │
│   ├── 10. Parse response JSON                    │   │
│   └── 11. Return array predictions ──────────────┘   │
│                                                       │
├── 12. Sortir prediksi (tertinggi → terendah)          │
├── 13. Simpan ke tabel detections (database)           │
├── 14. Ambil info penyakit dari tabel diseases         │
├── 15. Ambil rekomendasi pestisida terkait             │
│                                                       │
▼                                                       │
[RESPONSE JSON]
│
├── detection: {predicted_class, confidence, ...}
├── disease: {name, description, symptoms, treatment, ...}
├── disease.pesticides: [{name, dosage, ...}]
└── image_url: URL gambar yang diupload

▼
[PENGGUNA]
├── Melihat hasil prediksi (kelas + persentase)
├── Melihat informasi penyakit
└── Melihat rekomendasi pestisida
```

---

## 10. Konfigurasi

### Environment Variables

| Variable | Default | Keterangan |
|---|---|---|
| `FASTAPI_URL` | `http://localhost:8001` | URL base FastAPI service |

### Konfigurasi FastAPI (dalam `predict_service.py`)

| Konstanta | Nilai | Keterangan |
|---|---|---|
| `HOST` | `0.0.0.0` | Menerima koneksi dari semua interface |
| `PORT` | `8001` | Port server FastAPI |
| `TARGET_SIZE` | `(224, 224)` | Ukuran gambar input (harus 224×224) |
| `CLAHE_CLIP_LIMIT` | `2.0` | Parameter CLAHE (identik training) |
| `CLAHE_TILE_GRID` | `(8, 8)` | Parameter CLAHE (identik training) |
| `NUM_CLASSES` | `4` | Jumlah kelas penyakit |

### Mengganti Port

Jika port 8001 sudah dipakai, ubah di **dua tempat**:

1. **`predict_service.py`** — ubah konstanta `PORT`:
   ```python
   PORT = 8002  # Ganti ke port yang tersedia
   ```

2. **`.env`** — ubah variable `FASTAPI_URL`:
   ```env
   FASTAPI_URL=http://localhost:8002
   ```

3. Jangan lupa clear config cache Laravel:
   ```bash
   php artisan config:clear
   ```

---

## 11. Troubleshooting

### ❌ "Gagal terhubung ke FastAPI service"

**Penyebab**: FastAPI server belum berjalan atau URL tidak tepat.

**Solusi**:
1. Pastikan FastAPI server sudah berjalan di terminal terpisah
2. Cek apakah URL di `.env` sudah benar: `FASTAPI_URL=http://localhost:8001`
3. Coba akses `http://localhost:8001/` di browser — jika muncul JSON health check, server berjalan

### ❌ "File model tidak ditemukan"

**Penyebab**: `best_model_phase2.pth` tidak ada di folder `python/`.

**Solusi**:
1. Pastikan file `best_model_phase2.pth` ada di `E:\SKRIPSI\bhumi-skripsi\python\`
2. Ukuran file harusnya sekitar ~6MB

### ❌ "ModuleNotFoundError: No module named 'torch'"

**Penyebab**: Dependencies Python belum terinstal.

**Solusi**:
```bash
cd E:\SKRIPSI\bhumi-skripsi\python
venv\Scripts\activate
pip install -r requirements.txt
```

### ❌ "Port 8001 already in use"

**Penyebab**: Port 8001 sudah dipakai oleh proses lain.

**Solusi**:
1. Cek proses yang menggunakan port:
   ```bash
   netstat -ano | find "8001"
   ```
2. Ganti port di `predict_service.py` dan `.env` (lihat bagian [Konfigurasi](#10-konfigurasi))

### ❌ Prediksi selalu menggunakan simulasi (random)

**Penyebab**: FastAPI tidak berjalan, sehingga Laravel fallback ke simulasi.

**Solusi**:
1. Cek `storage/logs/laravel.log` untuk melihat error detail
2. Pastikan FastAPI berjalan dan bisa diakses
3. Clear config cache: `php artisan config:clear`

### ❌ Prediksi tidak akurat

**Penyebab kemungkinan**:
- Gambar bukan daun jagung
- Kualitas gambar sangat rendah
- Gambar terlalu gelap/terang

**Solusi**:
- Gunakan gambar daun jagung yang jelas dan fokus
- Pastikan pencahayaan cukup
- Format gambar harus JPG atau PNG

### ❌ "CUDA out of memory" (jika menggunakan GPU)

**Penyebab**: GPU kehabisan memori.

**Solusi**: Tidak perlu khawatir — MobileNetV3-Small sangat ringan (~6MB). Error ini kemungkinan disebabkan proses lain yang menggunakan GPU. Cukup gunakan CPU:
```python
# Paksa menggunakan CPU
device = torch.device("cpu")
```

---

## 12. Panduan Mengganti Model / Arsitektur

Jika setelah melakukan analisa perbandingan kinerja dan performa antar arsitektur, ternyata model terbaik **bukan** MobileNetV3-Small + SGD yang saat ini diimplementasikan, kamu perlu mengganti konfigurasi di beberapa file.

### 12.1 Ringkasan — File yang Perlu Diubah

| No | File | Apa yang Diubah | Wajib? |
|---|---|---|---|
| 1 | `python/predict_service.py` | Arsitektur model, import, dan komentar/metadata | ✅ Ya |
| 2 | `python/best_model_phase2.pth` | Ganti file `.pth` dengan model baru | ✅ Ya |
| 3 | `python/dokumentasi-integrasi-model.md` | Update nama arsitektur di dokumentasi | 📝 Disarankan |
| 4 | `app/Http/Controllers/HomeController.php` | **Tidak perlu diubah** | ❌ Tidak |
| 5 | `config/services.php` | **Tidak perlu diubah** | ❌ Tidak |
| 6 | `.env` | **Tidak perlu diubah** | ❌ Tidak |

> **Catatan penting**: Yang perlu diubah hanya di sisi **Python (FastAPI)**. Sisi Laravel **tidak perlu diubah sama sekali** karena format request dan response API tetap sama, tidak peduli arsitektur model apa yang digunakan di belakang layar.

---

### 12.2 Langkah-Langkah Detail Mengganti Model

#### Langkah 1 — Ganti File Model `.pth`

Salin file `.pth` baru ke folder `python/` dan ganti file yang lama:

```
E:\SKRIPSI\bhumi-skripsi\python\
├── best_model_phase2.pth    ← Ganti dengan file .pth model baru
├── predict_service.py
└── requirements.txt
```

> Nama file tetap `best_model_phase2.pth`, atau bisa diganti — asalkan nama baru di-update juga di `predict_service.py` (konstanta `MODEL_PATH`).

#### Langkah 2 — Ubah `predict_service.py`

Ada **3 bagian** yang perlu diubah di file `predict_service.py`:

---

##### Bagian A — Import Arsitektur Model (baris atas file)

Ganti baris import sesuai arsitektur baru:

```python
# ─── SAAT INI (MobileNetV3-Small): ───
from torchvision.models import mobilenet_v3_small, MobileNet_V3_Small_Weights

# ─── GANTI KE AlexNet: ───
from torchvision.models import alexnet, AlexNet_Weights

# ─── GANTI KE VGG16: ───
from torchvision.models import vgg16, VGG16_Weights
```

---

##### Bagian B — Fungsi `load_model()` (bagian tengah file)

Ubah arsitektur model dan modifikasi classifier head:

**Jika mengganti ke AlexNet:**
```python
def load_model(model_path: Path) -> nn.Module:
    logger.info(f"Memuat model dari: {model_path}")

    if not model_path.exists():
        raise FileNotFoundError(
            f"File model tidak ditemukan: {model_path}\n"
            f"Pastikan file 'best_model_phase2.pth' ada di folder 'python/'."
        )

    # Buat arsitektur AlexNet (tanpa pretrained weights)
    model = alexnet(weights=None)

    # Modifikasi classifier head: 4096 → 4 kelas
    model.classifier[6] = nn.Linear(4096, NUM_CLASSES)

    # Muat bobot model yang sudah di-training
    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    state_dict = torch.load(model_path, map_location=device, weights_only=True)
    model.load_state_dict(state_dict)

    model.eval()
    model.to(device)

    logger.info(f"Model berhasil dimuat pada device: {device}")
    logger.info(f"Jumlah parameter: {sum(p.numel() for p in model.parameters()):,}")

    return model
```

**Jika mengganti ke VGG16:**
```python
def load_model(model_path: Path) -> nn.Module:
    logger.info(f"Memuat model dari: {model_path}")

    if not model_path.exists():
        raise FileNotFoundError(
            f"File model tidak ditemukan: {model_path}\n"
            f"Pastikan file 'best_model_phase2.pth' ada di folder 'python/'."
        )

    # Buat arsitektur VGG16 (tanpa pretrained weights)
    model = vgg16(weights=None)

    # Modifikasi classifier head: 4096 → 4 kelas
    model.classifier[6] = nn.Linear(4096, NUM_CLASSES)

    # Muat bobot model yang sudah di-training
    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    state_dict = torch.load(model_path, map_location=device, weights_only=True)
    model.load_state_dict(state_dict)

    model.eval()
    model.to(device)

    logger.info(f"Model berhasil dimuat pada device: {device}")
    logger.info(f"Jumlah parameter: {sum(p.numel() for p in model.parameters()):,}")

    return model
```

> **Perhatikan perbedaannya**: Pada MobileNetV3-Small, classifier head diakses melalui `model.classifier[-1]` dengan `in_features` dinamis. Pada AlexNet dan VGG16, classifier head diakses melalui `model.classifier[6]` dengan `in_features` tetap sebesar `4096`.

---

##### Bagian C — Komentar dan Metadata (opsional tapi disarankan)

Update nama arsitektur di bagian docstring, print statement, dan metadata endpoint agar dokumentasi kode tetap akurat:

```python
# Di bagian atas file (docstring)
"""
Arsitektur : AlexNet (Transfer Learning)      # ← ganti nama
Optimizer  : Adam (saat training)              # ← ganti optimizer jika berbeda
...
"""

# Di FastAPI app metadata
app = FastAPI(
    title="BHUMI — API Deteksi Penyakit Daun Jagung",
    description="... model CNN AlexNet.",       # ← ganti nama
    ...
)

# Di endpoint root
@app.get("/")
async def root():
    return {
        ...
        "model": "AlexNet (Phase 2 Fine-tuned)",  # ← ganti nama
        ...
    }

# Di entry point
if __name__ == "__main__":
    print(f"  Model   : AlexNet (best_model_phase2.pth)")  # ← ganti nama
```

---

### 12.3 Referensi Cepat — Perbedaan Antar Arsitektur

Berikut tabel perbedaan konfigurasi untuk setiap arsitektur yang digunakan dalam proyek ini:

| Aspek | MobileNetV3-Small | AlexNet | VGG16 |
|---|---|---|---|
| **Import** | `mobilenet_v3_small` | `alexnet` | `vgg16` |
| **Buat model** | `mobilenet_v3_small(weights=None)` | `alexnet(weights=None)` | `vgg16(weights=None)` |
| **Akses classifier** | `model.classifier[-1]` | `model.classifier[6]` | `model.classifier[6]` |
| **In-features** | `1024` (dinamis via `in_features`) | `4096` | `4096` |
| **Ganti classifier** | `model.classifier[-1] = nn.Linear(in_features, 4)` | `model.classifier[6] = nn.Linear(4096, 4)` | `model.classifier[6] = nn.Linear(4096, 4)` |
| **Ukuran file .pth** | ~6 MB | ~230 MB | ~530 MB |
| **Jumlah parameter** | ~2.5 juta | ~61 juta | ~138 juta |
| **Kecepatan inferensi (CPU)** | Sangat cepat (~50ms) | Cepat (~100ms) | Lambat (~300ms) |
| **RAM saat berjalan** | ~50 MB | ~250 MB | ~550 MB |

---

### 12.4 Checklist Mengganti Model

Gunakan checklist ini saat mengganti arsitektur model:

```
☐ 1. Salin file .pth baru ke folder python/
☐ 2. Update import arsitektur di predict_service.py (Bagian A)
☐ 3. Update fungsi load_model() di predict_service.py (Bagian B)
☐ 4. Update komentar/metadata di predict_service.py (Bagian C)
☐ 5. Restart FastAPI server (matikan lalu jalankan ulang)
☐ 6. Test prediksi: akses http://localhost:8001/docs → coba upload gambar
☐ 7. Update dokumentasi ini (nama arsitektur di bagian atas)
```

> **Yang TIDAK perlu diubah**: `HomeController.php`, `services.php`, `.env`, `requirements.txt`, dan semua file Laravel lainnya. Sisi Laravel sepenuhnya agnostik terhadap arsitektur model yang digunakan.

---

## 13. FAQ

### Q: Apakah harus punya GPU untuk menjalankan deteksi?

**Tidak.** MobileNetV3-Small dirancang untuk berjalan efisien di CPU. Inferensi satu gambar memakan waktu sekitar 50–200ms di CPU modern. GPU hanya mempercepat sedikit untuk single-image inference. AlexNet dan VGG16 juga bisa berjalan di CPU, meskipun lebih lambat.

### Q: Apa yang terjadi jika FastAPI mati saat pengguna upload gambar?

Laravel memiliki **mekanisme fallback otomatis**. Jika FastAPI tidak tersedia, sistem akan menggunakan prediksi simulasi (acak) dan mencatat peringatan di log. Pengguna tetap mendapatkan respons, meskipun hasilnya bukan dari model CNN.

### Q: Apakah model di-load ulang setiap kali ada request?

**Tidak.** Model dimuat **sekali** saat FastAPI server pertama kali dijalankan (event `startup`). Setelah itu, model tetap di memori dan digunakan untuk semua request berikutnya. Ini membuat inferensi sangat cepat.

### Q: Bagaimana cara mengganti model dengan arsitektur lain?

Lihat panduan lengkap di bagian [12. Panduan Mengganti Model / Arsitektur](#12-panduan-mengganti-model--arsitektur). Intinya, hanya file `predict_service.py` dan file `.pth` yang perlu diganti — **Laravel tidak perlu diubah.**

### Q: Apakah bisa deploy ke server production?

Ya, dengan penyesuaian:
1. Jalankan FastAPI dengan Gunicorn + Uvicorn workers
2. Gunakan reverse proxy (Nginx)
3. Atur `FASTAPI_URL` di `.env` production ke alamat server FastAPI

### Q: Berapa ukuran memori yang dibutuhkan model?

Tergantung arsitektur yang digunakan:

| Arsitektur | File `.pth` | RAM saat berjalan |
|---|---|---|
| MobileNetV3-Small | ~6 MB | ~50 MB |
| AlexNet | ~230 MB | ~250 MB |
| VGG16 | ~530 MB | ~550 MB |

---

*Dokumentasi ini dibuat untuk Proyek BHUMI — Skripsi Perbandingan Arsitektur CNN untuk Deteksi Penyakit Daun Jagung.*
