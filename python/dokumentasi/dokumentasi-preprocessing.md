# Dokumentasi Pipeline Preprocessing Dataset Penyakit Daun Jagung

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Dependensi dan Library](#2-dependensi-dan-library)
3. [Konfigurasi dan Parameter Global](#3-konfigurasi-dan-parameter-global)
4. [Tahapan Pipeline Preprocessing](#4-tahapan-pipeline-preprocessing)
   - [4.1 Peningkatan Kontras — CLAHE](#41-peningkatan-kontras--clahe)
   - [4.2 Resize Gambar](#42-resize-gambar)
   - [4.3 Normalisasi Piksel](#43-normalisasi-piksel)
   - [4.4 Penyimpanan Hasil](#44-penyimpanan-hasil)
5. [Split Dataset](#5-split-dataset)
6. [Augmentasi Data Train](#6-augmentasi-data-train)
7. [Export ZIP](#7-export-zip)
8. [Fungsi Helper](#8-fungsi-helper)
9. [Entry Point — Alur Eksekusi Lengkap](#9-entry-point--alur-eksekusi-lengkap)
10. [Format File Gambar yang Didukung](#10-format-file-gambar-yang-didukung)
11. [Referensi](#11-referensi)

---

## 1. Gambaran Umum

File ini mendokumentasikan notebook `preprocessing-dataset-new.ipynb` yang berisi pipeline preprocessing lengkap untuk dataset penyakit daun jagung (*corn leaf disease*). Dataset terdiri dari 4 kelas:

| Kelas | Deskripsi |
|-------|-----------|
| `Corn_(maize)___Cercospora_leaf_spot Gray_leaf_spot` | Bercak daun abu-abu / *Gray leaf spot* |
| `Corn_(maize)___Common_rust_` | Karat jagung (*Common rust*) |
| `Corn_(maize)___Northern_Leaf_Blight` | Hawar daun utara (*Northern Leaf Blight*) |
| `Corn_(maize)___healthy` | Daun jagung sehat |

### Mengapa Background Removal Dihilangkan?

> **Masalah:** HSV thresholding dengan rentang warna hijau menghilangkan area penyakit (bercak kuning, karat coklat, hawar) yang justru merupakan **informasi kritis** untuk klasifikasi. Hasilnya adalah gambar berlubang-lubang hitam yang membuang data penting.
>
> **Solusi:** Background removal tidak digunakan. CNN modern mampu secara otomatis belajar memfokuskan perhatian pada area relevan (*attention mechanism*) tanpa perlu segmentasi manual.

### Urutan Tahapan Pipeline

```
Input Dataset
    │
    ▼
[1] CLAHE                → Peningkatan kontras adaptif lokal
    │
    ▼
[2] Resize 224×224       → Standarisasi ukuran input CNN
    │
    ▼
[3] Normalisasi [0, 1]   → Konversi float32, piksel skala 0–1
    │
    ▼
[4] Split Dataset        → Stratified split 80 : 10 : 10
    │
    ▼
[5] Augmentasi Train     → Target tetap 2000 gambar per kelas
    │
    ▼
[6] Export ZIP           → Mengemas hasil ke .zip
```

---

## 2. Dependensi dan Library

| Library | Versi | Fungsi |
|---------|-------|--------|
| `opencv-python` (`cv2`) | ≥ 4.8 | Pembacaan gambar, operasi image processing (HSV thresholding, morfologi, CLAHE, resize, flip, rotate, affine) |
| `numpy` (`np`) | ≥ 1.24 | Operasi array numerik, manipulasi piksel |
| `pathlib` | built-in | Manajemen path file lintas platform |
| `os` | built-in | Operasi sistem file (cek path, buat direktori) |
| `shutil` | built-in | Salin file antar direktori |
| `zipfile` | built-in | Membuat arsip ZIP |
| `random` | built-in | Pengambilan sampel acak untuk augmentasi |
| `tqdm` | ≥ 4.65 | Progress bar pada iterasi batch |
| `sklearn.model_selection` | ≥ 1.3 | `train_test_split` untuk stratified split |
| `datetime` | built-in | Pembuatan timestamp untuk nama file ZIP |

---

## 3. Konfigurasi dan Parameter Global

Semua parameter dikonfigurasi sebagai konstanta di awal notebook agar mudah dimodifikasi tanpa menyentuh logika fungsi.

### Path

| Konstanta | Nilai Default | Deskripsi |
|-----------|---------------|-----------|
| `INPUT_DIR` | `/kaggle/input/.../plantdiseasdataset_corn_ori_process_pict` | Direktori dataset asli (sebelum preprocessing) |
| `OUTPUT_DIR` | `/kaggle/working/preprocessed` | Output hasil preprocessing (step 1–4) |
| `SPLIT_OUTPUT_DIR` | `/kaggle/working/split` | Output hasil split train/valid/test |
| `ZIP_OUTPUT_DIR` | `/kaggle/working` | Direktori tempat file ZIP disimpan |
| `ZIP_FILENAME` | `None` | Nama file ZIP; `None` = generate otomatis dengan timestamp |

### Format File Gambar

| Konstanta | Nilai |
|-----------|-------|
| `IMAGE_EXTENSIONS` | `{'.jpg', '.jpeg', '.png', '.JPG', '.JPEG', '.PNG'}` |

Set ini mencakup variasi huruf besar dan kecil agar semua file gambar terdeteksi meski nama file menggunakan kapitalisasi berbeda (umum terjadi pada gambar dari kamera digital/smartphone).

### Parameter Preprocessing

| Konstanta | Nilai Default | Deskripsi |
|-----------|---------------|-----------|
| `TARGET_SIZE` | `(224, 224)` | Ukuran target resize (width × height) |
| `CLAHE_CLIP_LIMIT` | `2.0` | Batas amplifikasi kontras CLAHE |
| `CLAHE_TILE_GRID` | `(8, 8)` | Ukuran grid tile CLAHE |
| `JPEG_QUALITY` | `95` | Kualitas JPEG saat menyimpan (1–100) |

### Parameter Split Dataset

| Konstanta | Nilai Default | Deskripsi |
|-----------|---------------|-----------|
| `TRAIN_RATIO` | `0.80` | Proporsi data training |
| `VALID_RATIO` | `0.10` | Proporsi data validasi |
| `RANDOM_SEED` | `42` | Seed acak untuk reprodusibilitas |

> **Catatan:** `TEST_RATIO` bersifat implisit: `1.0 - TRAIN_RATIO - VALID_RATIO = 0.10`

### Parameter Augmentasi

| Konstanta | Nilai Default | Deskripsi |
|-----------|---------------|-----------|
| `AUG_TARGET_PER_CLASS` | `2000` | Target jumlah gambar per kelas setelah augmentasi |
| `AUG_ROTATION_RANGE` | `20` | Rotasi maksimum ± derajat |
| `AUG_BRIGHTNESS_RANGE` | `0.15` | Delta kecerahan proporsional (±15%) |
| `AUG_ZOOM_RANGE` | `0.15` | Zoom in maksimum (15%) |
| `AUG_SHIFT_RANGE` | `0.10` | Shift/translasi maksimum (±10% dimensi) |

---

## 4. Tahapan Pipeline Preprocessing

> **Catatan:** Step Background Removal **dihilangkan** dari pipeline. Alasan: HSV thresholding warna hijau secara tidak sengaja menghapus area bercak penyakit (kuning, coklat, karat) yang merupakan fitur diskriminatif utama untuk klasifikasi. Penelitian Mohanty et al. (2016) dan Ferentinos (2018) mengkonfirmasi bahwa CNN untuk deteksi penyakit tanaman tidak memerlukan background removal — model belajar secara otomatis.

### 4.1 Peningkatan Kontras — CLAHE

**Fungsi:** `apply_clahe(image: np.ndarray) → np.ndarray`

**Tujuan:** Meningkatkan kontras gambar secara adaptif dan lokal menggunakan CLAHE (*Contrast Limited Adaptive Histogram Equalization*), sehingga detail tekstur penyakit pada daun menjadi lebih terlihat jelas.

**Proses:**

```
Gambar BGR (setelah background removal)
    │
    ▼  cv2.cvtColor(BGR → Lab)
Lab Image (L=kecerahan, a=hijau-merah, b=biru-kuning)
    │
    ├──> Channel L (Luminance)
    │        │
    │        ▼  CLAHE(clipLimit=2.0, tileGridSize=(8,8))
    │    L enhanced
    │
    ▼  cv2.merge([L_enhanced, a, b])
Lab Enhanced
    │
    ▼  cv2.cvtColor(Lab → BGR)
Gambar BGR dengan kontras diperbaiki
```

**Parameter yang Digunakan:**

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `CLAHE_CLIP_LIMIT` | `2.0` | Batas amplifikasi kontras; nilai ≤ 2.0 mencegah amplifikasi noise berlebihan |
| `CLAHE_TILE_GRID` | `(8, 8)` | Grid 8×8 tile; setiap tile berukuran 28×28 piksel untuk gambar 224×224 — keseimbangan antara lokal dan global |

**Mengapa CLAHE pada Channel L di Ruang Lab?**
- **Ruang Lab** memisahkan kecerahan (*Lightness*, channel L) dari informasi warna (channel a dan b) secara perseptual, berbeda dari HSV/YCrCb yang kurang bersih dalam pemisahan ini.
- CLAHE hanya diterapkan pada channel **L** sehingga kontras ditingkatkan tanpa mengubah warna asli daun (hijau, kuning, coklat yang menjadi ciri penyakit).
- Ini penting agar warna yang menjadi ciri khas setiap penyakit (bercak kuning, karat merah-oranye, hawar coklat) tidak terdistorsi.

**Perbedaan CLAHE vs HE (Histogram Equalization):**

| Aspek | HE | CLAHE |
|-------|----|-------|
| Ruang lingkup | Global (seluruh gambar) | Lokal (per tile/region) |
| Amplifikasi noise | Tinggi | Terbatas (*clip limit*) |
| Cocok untuk | Gambar kontras rendah merata | Gambar dengan variasi kontras lokal |

**Referensi:**
- Zuiderveld, K. (1994). Contrast Limited Adaptive Histogram Equalization. *Graphics Gems IV*, 474–485. Academic Press.
- Pizer, S. M., et al. (1987). Adaptive histogram equalization and its variations. *Computer Vision, Graphics, and Image Processing*, 39(3), 355–368.
- OpenCV Documentation: [CLAHE](https://docs.opencv.org/4.x/d5/daf/tutorial_py_histogram_equalization.html)

---

### 4.2 Resize Gambar

**Fungsi:** `resize_image(image: np.ndarray, target_size: tuple = TARGET_SIZE) → np.ndarray`

**Tujuan:** Mengubah ukuran gambar ke dimensi standar `224×224` piksel yang diperlukan sebagai input model CNN.

**Parameter yang Digunakan:**

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `target_size` | `(224, 224)` | Width × Height dalam piksel |
| `interpolation` | `cv2.INTER_LANCZOS4` | Metode interpolasi kualitas tinggi |

**Mengapa 224×224 piksel?**
Ukuran 224×224 adalah standar de facto untuk model CNN berbasis ImageNet seperti VGG, ResNet, EfficientNet, dan MobileNet. Menggunakan ukuran ini memungkinkan penggunaan *pretrained weights* dari ImageNet langsung.

**Mengapa INTER_LANCZOS4?**
- LANCZOS4 menggunakan kernel 8×8 dengan fungsi sinc untuk interpolasi, menghasilkan gambar yang lebih tajam dan bebas artefak aliasing dibandingkan INTER_LINEAR (bilinear) atau INTER_AREA.
- Cocok untuk *downscaling* gambar resolusi tinggi ke 224×224 karena mempertahankan detail tekstur lebih baik.
- Trade-off: sedikit lebih lambat dari bilinear, namun perbedaannya tidak signifikan untuk skala dataset ini.

**Referensi:**
- He, K., Zhang, X., Ren, S., & Sun, J. (2016). Deep Residual Learning for Image Recognition. *CVPR 2016*.
- Deng, J., et al. (2009). ImageNet: A Large-Scale Hierarchical Image Database. *CVPR 2009*.
- OpenCV Documentation: [Geometric Image Transformations — resize](https://docs.opencv.org/4.x/da/d54/group__imgproc__transform.html#ga47a974309e9102f5f08231edc7e7529d)

---

### 4.3 Normalisasi Piksel

**Fungsi:** `normalize_image(image: np.ndarray) → np.ndarray`

**Tujuan:** Mengonversi nilai piksel dari rentang integer `[0, 255]` (uint8) ke float `[0.0, 1.0]` (float32) untuk kompatibilitas dengan framework deep learning.

**Rumus:**
```
pixel_normalized = pixel_uint8 / 255.0
```

**Parameter yang Digunakan:**

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| Dtype input | `uint8` | Integer 8-bit, rentang 0–255 |
| Dtype output | `float32` | Float 32-bit, rentang 0.0–1.0 |
| Pembagi | `255.0` | Nilai maksimum uint8 |

**Mengapa Normalisasi Harus Dilakukan Terakhir?**
Semua fungsi OpenCV (CLAHE, morphologyEx, resize, warpAffine, dll.) mengharapkan input bertipe `uint8`. Konversi ke `float32` hanya dilakukan di akhir pipeline, tepat sebelum data digunakan oleh model.

**Referensi:**
- LeCun, Y., et al. (1998). Gradient-Based Learning Applied to Document Recognition. *Proceedings of the IEEE*, 86(11), 2278–2324.
- Goodfellow, I., Bengio, Y., & Courville, A. (2016). *Deep Learning*. MIT Press. (Chapter 8: Optimization for Training Deep Models)

---

### 4.4 Penyimpanan Hasil

**Fungsi:** `save_preprocessed_image(image_normalized: np.ndarray, output_path: str) → bool`

**Tujuan:** Menyimpan gambar hasil preprocessing (float32 `[0,1]`) kembali ke format file JPEG di disk.

**Proses:**
```
float32 [0,1]
    │
    ▼  × 255.0 → clip(0,255) → astype(uint8)
uint8 [0,255]
    │
    ▼  cv2.imwrite(path, image, [IMWRITE_JPEG_QUALITY, 95])
File .jpg
```

**Parameter yang Digunakan:**

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `JPEG_QUALITY` | `95` | Kualitas JPEG (1=buruk, 100=lossless); 95 menjaga kualitas visual tinggi dengan kompresi memadai |

---

## 5. Split Dataset

**Fungsi:** `split_dataset(preprocessed_dir, split_dir, train_ratio, valid_ratio, seed) → dict`

**Tujuan:** Membagi dataset hasil preprocessing menjadi tiga subset: *train*, *valid*, dan *test* menggunakan stratified split untuk menjaga distribusi kelas di setiap subset.

### Skema Pembagian Data

| Subset | Proporsi | Fungsi |
|--------|----------|--------|
| **Train** | 80% | Digunakan untuk melatih model |
| **Valid** | 10% | Digunakan untuk validasi performa model selama training |
| **Test** | 10% | Digunakan untuk evaluasi akhir model (tidak tersentuh selama training) |

### Mekanisme Stratified Split

```
All Files (N total)
    │
    ▼  train_test_split(test_size=0.20, stratify=labels)
Train (80%)    |    Temp (20%)
                        │
                        ▼  train_test_split(test_size=0.50, stratify=labels)
                   Valid (10%)   |   Test (10%)
```

Stratified split memastikan setiap subset memiliki proporsi kelas yang sama dengan dataset asli, penting untuk dataset dengan distribusi kelas tidak seimbang (*imbalanced dataset*).

### Parameter yang Digunakan

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `train_ratio` | `0.80` | Proporsi training |
| `valid_ratio` | `0.10` | Proporsi validasi |
| `seed` | `42` | Random seed untuk reprodusibilitas |
| `stratify` | `all_labels` | Pastikan distribusi kelas seimbang di setiap split |

### Output Struktur Direktori

```
split/
├── train/
│   ├── Corn_(maize)___Cercospora_leaf_spot Gray_leaf_spot/
│   ├── Corn_(maize)___Common_rust_/
│   ├── Corn_(maize)___Northern_Leaf_Blight/
│   └── Corn_(maize)___healthy/
├── valid/
│   └── (struktur sama)
└── test/
    └── (struktur sama)
```

**Referensi:**
- Pedregosa, F., et al. (2011). Scikit-learn: Machine Learning in Python. *JMLR*, 12, 2825–2830.
- Géron, A. (2019). *Hands-On Machine Learning with Scikit-Learn, Keras, and TensorFlow* (2nd ed.). O'Reilly Media. (Chapter 2: End-to-End Machine Learning Project)

---

## 6. Augmentasi Data Train

**Fungsi:** `augment_train_data(split_dir, target_per_class, seed) → dict`

**Tujuan:** Menghasilkan gambar-gambar baru secara sintetis dari gambar asli pada subset *train* untuk mencapai target jumlah data yang tetap per kelas, mengatasi masalah *class imbalance* dan memperkaya variasi data training.

> **Penting:** Augmentasi **hanya** diterapkan pada subset `train`. Subset `valid` dan `test` tidak diaugmentasi agar evaluasi model tetap representatif terhadap data nyata.

### Strategi Augmentasi

**Fixed Target Strategy:**
- Setiap kelas pada subset train ditargetkan memiliki tepat **2000 gambar** setelah augmentasi.
- Kelas yang sudah memiliki ≥ 2000 gambar tidak diaugmentasi.
- Kelas yang memiliki < 2000 gambar di-augmentasi dengan cycling (gambar asli diulang secara acak) hingga mencapai target.

### Jenis Augmentasi

Augmentasi dipilih berdasarkan relevansi dengan kondisi lapangan pengambilan gambar daun jagung (*corn field imaging*):

#### 1. Flip Horizontal — `aug_flip_horizontal`

| Parameter | Nilai |
|-----------|-------|
| `flipCode` | `1` (horizontal) |

**Relevansi:** Daun jagung di lahan dapat menghadap ke kiri maupun ke kanan. Flip horizontal mensimulasikan variasi orientasi alami daun saat difoto dari berbagai posisi di lapangan.

#### 2. Flip Vertikal — `aug_flip_vertical`

| Parameter | Nilai |
|-----------|-------|
| `flipCode` | `0` (vertikal) |

**Relevansi:** Pengambilan gambar daun jagung dapat dilakukan dari atas (drone) maupun dari samping, menghasilkan variasi orientasi vertikal.

#### 3. Rotasi — `aug_rotate`

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `max_angle` | `AUG_ROTATION_RANGE = 20°` | Rotasi ± 20 derajat |
| `borderMode` | `cv2.BORDER_REFLECT` | Mengisi area kosong dengan refleksi piksel agar tidak ada area hitam |

**Relevansi:** Kamera atau drone di lahan jagung jarang dalam posisi sempurna tegak lurus, sehingga gambar sering memiliki sedikit kemiringan.

#### 4. Perubahan Kecerahan — `aug_brightness`

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `max_delta` | `AUG_BRIGHTNESS_RANGE = 0.15` | Delta ±15% dari skala 0–255 (≈ ±38 unit piksel) |

**Relevansi:** Kondisi pencahayaan di lahan jagung sangat bervariasi — pagi hari (cahaya redup), siang (cahaya terik), mendung, dll. Augmentasi kecerahan mensimulasikan variasi kondisi ini.

#### 5. Zoom — `aug_zoom`

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `max_zoom` | `AUG_ZOOM_RANGE = 0.15` | Zoom in hingga 15% |
| Metode | Random crop + resize LANCZOS4 | Dipotong dari posisi acak lalu dikembalikan ke 224×224 |

**Relevansi:** Jarak kamera ke daun jagung bervariasi di lapangan. Zoom in mensimulasikan pengambilan gambar dari jarak lebih dekat.

#### 6. Translasi / Shift — `aug_shift`

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `max_shift` | `AUG_SHIFT_RANGE = 0.10` | Geser maksimum ±10% dari dimensi gambar |
| `borderMode` | `cv2.BORDER_REFLECT` | Isi area kosong dengan refleksi |

**Relevansi:** Posisi daun dalam frame kamera tidak selalu terpusat. Translasi mensimulasikan variasi posisi daun dalam frame.

#### 7. Gaussian Noise — `aug_gaussian_noise`

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `std` | `0.02` | Standar deviasi noise = 0.02 × 255 ≈ 5.1 unit piksel |

**Relevansi:** Sensor kamera murah (smartphone, kamera lapangan) sering menghasilkan noise pada kondisi cahaya rendah atau resolusi rendah.

#### 8. Koreksi Gamma — `aug_gamma_correction`

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `gamma_range` | `(0.7, 1.4)` | Gamma < 1.0 mencerahkan; gamma > 1.0 menggelapkan |

**Relevansi:** Mensimulasikan variasi respons sensor kamera yang berbeda-beda dan kondisi eksposur yang beragam di lapangan.

### Kombinasi Augmentasi Acak

**Fungsi:** `apply_random_augmentation(image, min_ops=1, max_ops=3) → np.ndarray`

Setiap gambar augmentasi dihasilkan dengan memilih **1–3 fungsi augmentasi secara acak** dari pool dan menerapkannya secara berurutan. Pendekatan ini menghasilkan variasi yang lebih beragam dibandingkan menerapkan augmentasi tunggal.

### Penamaan File Augmentasi

```
{nama_file_asli}_aug{index:04d}.jpg
Contoh: corn_rust_001_aug0023.jpg
```

**Parameter yang Digunakan:**

| Parameter | Nilai | Penjelasan |
|-----------|-------|------------|
| `target_per_class` | `AUG_TARGET_PER_CLASS = 2000` | Target tetap per kelas |
| `seed` | `RANDOM_SEED = 42` | Seed untuk reprodusibilitas |
| `JPEG_QUALITY` | `95` | Kualitas JPEG file augmentasi |

**Referensi:**
- Shorten, C., & Khoshgoftaar, T. M. (2019). A survey on Image Data Augmentation for Deep Learning. *Journal of Big Data*, 6(1), 1–48. https://doi.org/10.1186/s40537-019-0197-0
- Mikołajczyk, A., & Grochowski, M. (2018). Data augmentation for improving deep learning in image classification problem. *IIPHDM 2018*, 117–122.
- Taylor, L., & Nitschke, G. (2017). Improving Deep Learning using Generic Data Augmentation. arXiv:1708.06020.

---

## 7. Export ZIP

**Fungsi:** `export_to_zip(output_dir, zip_output_dir, zip_filename) → str`

**Tujuan:** Mengemas seluruh hasil preprocessing (termasuk split dan augmentasi) ke dalam satu file ZIP untuk memudahkan distribusi dan pengunduhan dari Kaggle.

**Parameter yang Digunakan:**

| Parameter | Nilai Default | Penjelasan |
|-----------|---------------|------------|
| `output_dir` | `SPLIT_OUTPUT_DIR` | Direktori yang dikemas |
| `zip_output_dir` | `ZIP_OUTPUT_DIR = '/kaggle/working'` | Lokasi file ZIP |
| `zip_filename` | `ZIP_FILENAME = None` | `None` = nama otomatis `preprocessed_dataset_YYYYMMDD_HHMMSS` |
| Kompresi | `zipfile.ZIP_DEFLATED` | Kompresi DEFLATE (gzip-based) untuk efisiensi ukuran |

---

## 8. Fungsi Helper

### `get_image_files(directory: Path) → list`

**Tujuan:** Mengumpulkan semua file gambar dalam direktori berdasarkan `IMAGE_EXTENSIONS`, mencakup variasi huruf besar dan kecil secara eksplisit.

**Mengapa Diperlukan?**
Metode `directory.glob("*.jpg")` bersifat *case-sensitive* di sistem Linux/Kaggle. File bernama `image.JPG` atau `image.PNG` tidak akan terdeteksi oleh glob `*.jpg` atau `*.png`. Fungsi ini mengiterasi semua ekstensi dalam `IMAGE_EXTENSIONS` untuk memastikan tidak ada file yang terlewat.

```python
IMAGE_EXTENSIONS = {'.jpg', '.jpeg', '.png', '.JPG', '.JPEG', '.PNG'}
```

**Mekanisme deduplikasi:** Menggunakan dictionary dengan `f.resolve()` sebagai key untuk menghapus entri duplikat yang mungkin muncul pada sistem file *case-insensitive* (Windows).

---

## 9. Entry Point — Alur Eksekusi Lengkap

```python
if __name__ == "__main__":
    # Step 1–4: Background Removal → CLAHE → Resize → Normalisasi
    prep_summary = preprocess_dataset(INPUT_DIR, OUTPUT_DIR)

    # Step 5: Stratified Split 80:10:10
    split_summary = split_dataset(OUTPUT_DIR, SPLIT_OUTPUT_DIR)

    # Step 6: Augmentasi train → target 2000 per kelas
    aug_summary = augment_train_data(SPLIT_OUTPUT_DIR,
                                     target_per_class=AUG_TARGET_PER_CLASS)

    # Step 7: Export ZIP (hanya jika tidak ada gambar gagal)
    if prep_summary["total_failed"] == 0:
        export_to_zip(SPLIT_OUTPUT_DIR, ZIP_OUTPUT_DIR, ZIP_FILENAME)
```

**Kondisi Guard Export ZIP:** File ZIP hanya dibuat jika tidak ada gambar yang gagal diproses pada step 1–4. Jika ada kegagalan, pesan peringatan ditampilkan dan ZIP tidak dibuat.

---

## 10. Format File Gambar yang Didukung

| Ekstensi | Keterangan |
|----------|------------|
| `.jpg` | JPEG lowercase |
| `.jpeg` | JPEG alternatif lowercase |
| `.png` | PNG lowercase |
| `.JPG` | JPEG uppercase (umum dari kamera digital) |
| `.JPEG` | JPEG uppercase |
| `.PNG` | PNG uppercase |

Penambahan ekstensi uppercase diperlukan karena dataset asli kemungkinan mengandung file dari berbagai sumber (kamera digital, smartphone, web scraping) yang menggunakan konvensi penamaan berbeda-beda.

---

## 11. Referensi

### Preprocessing

1. **HSV Color Space & Thresholding**
   - Bradski, G., & Kaehler, A. (2008). *Learning OpenCV: Computer Vision with the OpenCV Library*. O'Reilly Media.
   - OpenCV. (2024). *Color Space Conversions*. https://docs.opencv.org/4.x/d8/d01/group__imgproc__color__conversions.html

2. **Morphological Operations**
   - Serra, J. (1982). *Image Analysis and Mathematical Morphology*. Academic Press.
   - OpenCV. (2024). *Morphological Transformations*. https://docs.opencv.org/4.x/d9/d61/tutorial_py_morphological_ops.html

3. **CLAHE (Contrast Limited Adaptive Histogram Equalization)**
   - Zuiderveld, K. (1994). Contrast Limited Adaptive Histogram Equalization. *Graphics Gems IV*, 474–485. Academic Press.
   - Pizer, S. M., et al. (1987). Adaptive histogram equalization and its variations. *Computer Vision, Graphics, and Image Processing*, 39(3), 355–368.
   - OpenCV. (2024). *Histogram Equalization*. https://docs.opencv.org/4.x/d5/daf/tutorial_py_histogram_equalization.html

4. **Resize & Interpolasi**
   - He, K., Zhang, X., Ren, S., & Sun, J. (2016). Deep Residual Learning for Image Recognition. *CVPR 2016*.
   - OpenCV. (2024). *Geometric Image Transformations*. https://docs.opencv.org/4.x/da/d54/group__imgproc__transform.html

5. **Normalisasi**
   - Goodfellow, I., Bengio, Y., & Courville, A. (2016). *Deep Learning*. MIT Press.
   - LeCun, Y., et al. (1998). Efficient BackProp. *Neural Networks: Tricks of the Trade*, 9–50.

### Dataset Splitting

6. **Stratified Split**
   - Pedregosa, F., et al. (2011). Scikit-learn: Machine Learning in Python. *JMLR*, 12, 2825–2830.
   - Géron, A. (2019). *Hands-On Machine Learning with Scikit-Learn, Keras, and TensorFlow* (2nd ed.). O'Reilly Media.

### Augmentasi Data

7. **Survey Augmentasi**
   - Shorten, C., & Khoshgoftaar, T. M. (2019). A survey on Image Data Augmentation for Deep Learning. *Journal of Big Data*, 6(1), 1–48. https://doi.org/10.1186/s40537-019-0197-0

8. **Augmentasi untuk Penyakit Tanaman**
   - Mohanty, S. P., Hughes, D. P., & Salathé, M. (2016). Using Deep Learning for Image-Based Plant Disease Detection. *Frontiers in Plant Science*, 7, 1419. https://doi.org/10.3389/fpls.2016.01419
   - Ferentinos, K. P. (2018). Deep learning models for plant disease detection and diagnosis. *Computers and Electronics in Agriculture*, 145, 311–318.

9. **Class Imbalance**
   - Buda, M., Maki, A., & Mazurowski, M. A. (2018). A systematic study of the class imbalance problem in convolutional neural networks. *Neural Networks*, 106, 249–259.
   - Chawla, N. V., et al. (2002). SMOTE: Synthetic Minority Over-sampling Technique. *JAIR*, 16, 321–357.

10. **Gaussian Noise & Gamma Correction**
    - Mikołajczyk, A., & Grochowski, M. (2018). Data augmentation for improving deep learning in image classification problem. *IIPHDM 2018*, 117–122.
    - Taylor, L., & Nitschke, G. (2017). Improving Deep Learning using Generic Data Augmentation. arXiv:1708.06020.

---

*Dokumentasi ini dibuat untuk notebook `preprocessing-dataset-new.ipynb` pada penelitian skripsi deteksi penyakit daun jagung.*
