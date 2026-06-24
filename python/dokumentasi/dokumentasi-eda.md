# Dokumentasi Hasil Exploratory Data Analysis (EDA)
## Dataset Penyakit Daun Jagung (*Corn/Maize Leaf Disease*)

---

## 1. Pendahuluan

Proses **EDA** (*Exploratory Data Analysis*) merupakan proses awal yang digunakan untuk **menganalisa karakteristik data** yang akan digunakan untuk tahap pengolahan model. Proses EDA merupakan proses penting agar nantinya kita dapat menentukan teknik apa yang cocok untuk diterapkan pada tahap selanjutnya yaitu pada tahap *preprocessing* maupun *modeling* berdasarkan dari kondisi data yang dimiliki.

### 1.1 Informasi Dataset

| Atribut | Detail |
|---|---|
| **Sumber** | Kaggle — `salmaaida/plantdisease-dataset-corn-ori-process` |
| **Jenis Data** | Gambar (Image Dataset) |
| **Total Gambar** | 3.852 file |
| **Jumlah Kelas** | 4 kelas |
| **Format File** | `.jpg` (100%) |
| **Mode Warna** | RGB (100%) |
| **Ukuran Gambar** | 256 × 256 piksel (seragam) |

### 1.2 Label Kelas

Dataset terdiri dari 4 kelas penyakit daun jagung:

| No | Nama Folder Asli | Label Kelas |
|---|---|---|
| 1 | `Corn_(maize)___Cercospora_leaf_spot Gray_leaf_spot` | Gray Leaf Spot |
| 2 | `Corn_(maize)___Common_rust_` | Common Rust |
| 3 | `Corn_(maize)___Northern_Leaf_Blight` | Northern Leaf Blight |
| 4 | `Corn_(maize)___healthy` | Healthy |

---

## 2. Distribusi Kelas pada Dataset

### 2.1 Hasil Distribusi

| Kelas | Jumlah Gambar | Persentase |
|---|---:|---:|
| **Common Rust** | 1.192 | 30,9% |
| **Healthy** | 1.162 | 30,2% |
| **Northern Leaf Blight** | 985 | 25,6% |
| **Gray Leaf Spot** | 513 | 13,3% |
| **TOTAL** | **3.852** | **100,0%** |

### 2.2 Analisis Distribusi

- **Kelas terbanyak**: Common Rust (1.192 gambar)
- **Kelas tersedikit**: Gray Leaf Spot (513 gambar)
- **Rasio imbalance**: **2,32x**
- **Kesimpulan**: Dataset **TIDAK seimbang** (rasio > 1.5x)

### 2.3 Implikasi untuk Preprocessing

Ketidakseimbangan kelas ini perlu ditangani pada tahap preprocessing/training. Beberapa strategi yang dapat diterapkan:
1. **Data Augmentation** — Menambah jumlah data pada kelas minoritas (terutama *Gray Leaf Spot*)
2. **Class Weight** — Memberikan bobot lebih besar pada kelas minoritas saat training
3. **Oversampling/Undersampling** — Menyeimbangkan jumlah data antar kelas

---

## 3. Format File dan Mode Warna

### 3.1 Distribusi Format File

| Ekstensi | Jumlah | Persentase |
|---|---:|---:|
| `.jpg` | 3.852 | 100,0% |

### 3.2 Hasil Analisis Mode Warna

- **Mode warna yang ditemukan**: RGB — 200 file (dari 200 sampel yang diuji)
- **File corrupt/tidak terbaca**: Tidak ada
- **File non-gambar**: 0 file

### 3.3 Kesimpulan

- Seluruh gambar menggunakan **format `.jpg`** dan **mode warna RGB** yang konsisten
- **Tidak ditemukan file corrupt** maupun file non-gambar di dalam dataset
- Dataset sudah dalam kondisi bersih dan siap digunakan dari sisi format file

---

## 4. Keseragaman Ukuran Gambar

### 4.1 Statistik Ukuran Gambar per Kelas

| Kelas | Min (W×H) | Max (W×H) | Dominan (W×H) |
|---|---|---|---|
| Gray Leaf Spot | 256×256 | 256×256 | 256×256 |
| Common Rust | 256×256 | 256×256 | 256×256 |
| Northern Leaf Blight | 256×256 | 256×256 | 256×256 |
| Healthy | 256×256 | 256×256 | 256×256 |

### 4.2 Hasil Analisis

- **Jumlah ukuran unik**: 1 (hanya 256×256)
- **Width rata-rata**: 256 px
- **Height rata-rata**: 256 px
- **Kesimpulan**: Semua gambar sudah **seragam** (256×256 piksel)

### 4.3 Implikasi untuk Preprocessing

Meskipun ukuran sudah seragam (256×256), gambar perlu di-**resize ke 224×224** agar sesuai dengan standar input arsitektur CNN yang digunakan (*VGG16, AlexNet, MobileNetV3*) yang menggunakan input ukuran **224×224 piksel**.

---

## 5. Kondisi Background Gambar

### 5.1 Rata-rata Proporsi Latar Belakang per Kelas

| Kelas | Proporsi Background |
|---|---:|
| Gray Leaf Spot | 36,2% |
| Common Rust | 53,3% |
| Northern Leaf Blight | 63,8% |
| Healthy | 23,8% |

### 5.2 Hasil Analisis

- **Rata-rata background keseluruhan**: **44,3%**
- **Kesimpulan**: Proporsi background **cukup besar** (> 20%)

### 5.3 Implikasi untuk Preprocessing

Proporsi background yang besar (rata-rata 44,3%) menunjukkan bahwa gambar tidak hanya berisi area daun, tetapi juga banyak area non-daun (tanah, langit, batang, dll.). Hal ini dapat mengganggu proses klasifikasi karena model mungkin belajar dari fitur background alih-alih fitur penyakit pada daun.

**Teknik yang direkomendasikan**:
- **Background Removal** menggunakan HSV thresholding untuk mengisolasi area daun dan menghapus background
- Pendekatan ini menggunakan segmentasi berbasis warna hijau daun pada ruang warna HSV

---

## 6. Distribusi Warna Gambar (RGB)

### 6.1 Statistik Mean & Standard Deviation per Channel RGB

| Kelas | Mean Blue | Mean Green | Mean Red | Std Blue | Std Green | Std Red |
|---|---:|---:|---:|---:|---:|---:|
| Common Rust | 69,43 | 98,44 | 90,88 | 55,16 | 68,19 | 64,93 |
| Gray Leaf Spot | 99,42 | 124,53 | 119,18 | 46,99 | 40,30 | 45,13 |
| Healthy | 125,58 | 166,77 | 131,76 | 47,55 | 38,70 | 45,37 |
| Northern Leaf Blight | 101,39 | 123,13 | 116,51 | 41,88 | 31,26 | 37,64 |

### 6.2 Analisis Distribusi Warna

1. **Kelas Healthy** memiliki nilai mean **tertinggi** di semua channel, khususnya channel Green (166,77) — menunjukkan daun yang sehat memiliki warna hijau yang lebih kuat
2. **Kelas Common Rust** memiliki nilai mean **terendah** di semua channel — gambar cenderung lebih gelap
3. **Kelas Common Rust** juga memiliki **standar deviasi tertinggi** — variasi warna paling besar, mencerminkan karakteristik penyakit karat (rust) yang menampilkan variasi warna karat-oranye-coklat
4. **Kelas Northern Leaf Blight** memiliki **standar deviasi terendah** — variasi warna paling rendah, gambar lebih seragam

### 6.3 Implikasi untuk Preprocessing

Perbedaan distribusi warna antar kelas menunjukkan bahwa:
- Warna merupakan **fitur diskriminatif** yang penting untuk klasifikasi
- Proses **normalisasi** gambar perlu dilakukan sebelum training (normalisasi ke range [0, 1])
- Variasi distribusi warna yang tinggi pada beberapa kelas menunjukkan perlunya model yang mampu menangkap berbagai variasi tersebut

---

## 7. Analisis Noise

### 7.1 Ringkasan Noise per Kelas

| Kelas | Laplacian Variance (Mean) | SNR (Mean) |
|---|---:|---:|
| Gray Leaf Spot | 1.309,30 | 3,31 |
| Common Rust | 2.561,14 | 1,29 |
| Northern Leaf Blight | 1.939,41 | 3,34 |
| Healthy | 712,98 | 7,30 |

> **Keterangan metrik:**
> - **Laplacian Variance**: Semakin tinggi → gambar semakin tajam/detail
> - **SNR (Signal-to-Noise Ratio)**: Semakin tinggi → kualitas sinyal lebih baik (noise lebih rendah)

### 7.2 Hasil Analisis

- **Rata-rata Laplacian Variance keseluruhan**: **1.630,71**
- **Kesimpulan**: Gambar relatif **tajam**, noise **minimal**

### 7.3 Detail Temuan

1. **Common Rust** memiliki Laplacian Variance tertinggi (2.561,14) tetapi SNR terendah (1,29) — gambar tajam tetapi memiliki banyak variasi intensitas akibat tekstur penyakit karat
2. **Healthy** memiliki Laplacian Variance terendah (712,98) dan SNR tertinggi (7,30) — daun sehat cenderung memiliki permukaan yang lebih halus/seragam
3. Secara umum, noise pada dataset **bukan masalah kritis**, namun proses filtering tetap direkomendasikan sebagai langkah preventif

### 7.4 Implikasi untuk Preprocessing

Meskipun noise minimal, penerapan **Adaptive Median Filter** tetap direkomendasikan untuk:
- Mengurangi noise halus tanpa merusak detail tepi (*edge*)
- Meningkatkan kualitas input ke model CNN
- Memastikan konsistensi kualitas gambar di seluruh dataset

---

## 8. Analisis Kualitas Gambar

### 8.1 Ringkasan Kualitas Gambar per Kelas

| Kelas | Brightness | Contrast | Sharpness | Gambar Bermasalah |
|---|---:|---:|---:|---|
| Gray Leaf Spot | 50,7 | 38,0 | 2.968,9 | 68 (34,0%) |
| Common Rust | 39,0 | 63,0 | 2.900,4 | 24 (12,0%) |
| Northern Leaf Blight | 50,3 | 32,0 | 1.741,3 | 85 (42,5%) |
| Healthy | 64,0 | 32,1 | 1.178,3 | 124 (62,0%) |

> **Threshold klasifikasi kualitas yang digunakan:**
> - Brightness: gelap < 30, overexposed > 70
> - Contrast: rendah < 30
> - Sharpness: blur < 50

### 8.2 Jenis Masalah yang Ditemukan

| Jenis Masalah | Jumlah | Persentase |
|---|---:|---:|
| Kontras rendah | 245 | 30,6% |
| Overexposed | 57 | 7,1% |
| Gelap | 26 | 3,2% |
| Blur | 21 | 2,6% |

### 8.3 Hasil Rata-rata Kualitas

- **Rata-rata brightness keseluruhan**: 51,0 / 100
- **Rata-rata contrast keseluruhan**: 41,3
- **Total gambar bermasalah**: **301 dari 800 (37,6%)**

### 8.4 Analisis Detail

1. **Kelas Healthy** memiliki persentase masalah tertinggi (**62,0%**) — dominan masalah kontras rendah dan overexposed, kemungkinan karena daun hijau sehat yang terkena pencahayaan berlebih
2. **Kelas Common Rust** memiliki persentase masalah terendah (**12,0%**) — gambar cenderung memiliki kontras lebih baik karena variasi warna penyakit
3. **Masalah utama**: Kontras rendah (30,6%) — merupakan masalah terbesar yang perlu ditangani

### 8.5 Implikasi untuk Preprocessing

Temuan ini memperkuat kebutuhan langkah-langkah preprocessing berikut:
- **Background removal** untuk menghapus area non-daun yang menurunkan kontras keseluruhan gambar
- **Adaptive Median Filter** untuk meningkatkan kualitas gambar yang blur
- **Normalisasi** untuk menstandarisasi range nilai piksel

---

## 9. Ringkasan Temuan dan Rekomendasi Preprocessing

### 9.1 Ringkasan Temuan Utama

| No | Aspek Analisis | Temuan | Status |
|---|---|---|---|
| 1 | Distribusi Kelas | Tidak seimbang (rasio 2,32x) | ⚠️ Perlu penanganan |
| 2 | Format File | Seragam (.jpg, RGB) | ✅ Baik |
| 3 | Ukuran Gambar | Seragam (256×256) | ✅ Baik |
| 4 | Background | Proporsi besar (44,3%) | ⚠️ Perlu penanganan |
| 5 | Distribusi Warna | Berbeda antar kelas (fitur diskriminatif) | ℹ️ Informatif |
| 6 | Noise | Minimal (Lap.Var = 1.630,71) | ✅ Baik |
| 7 | Kualitas Gambar | 37,6% bermasalah (dominan kontras rendah) | ⚠️ Perlu penanganan |

### 9.2 Pipeline Preprocessing yang Direkomendasikan

Berdasarkan hasil analisis EDA, berikut pipeline preprocessing yang direkomendasikan:

```
Input Gambar (256×256, RGB, .jpg)
        │
        ▼
┌─────────────────────────┐
│  1. Background Removal  │  ← Mengatasi proporsi background 44,3%
│     (HSV Thresholding)  │     Menggunakan mask warna hijau daun
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────────┐
│  2. Adaptive Median Filter  │  ← Reduksi noise & peningkatan kualitas
│     (Kernel size = 3)       │     Menangani 37,6% gambar bermasalah
└──────────┬──────────────────┘
           │
           ▼
┌──────────────────────────┐
│  3. Resize ke 224×224    │  ← Menyesuaikan input CNN
│     (dari 256×256)       │     (VGG16, AlexNet, MobileNetV3)
└──────────┬───────────────┘
           │
           ▼
┌──────────────────────────┐
│  4. Normalisasi [0, 1]   │  ← Standarisasi range nilai piksel
│     (pixel / 255.0)      │     Mempercepat konvergensi training
└──────────┬───────────────┘
           │
           ▼
    Output Gambar
   (224×224, float32)
```

### 9.3 Strategi Tambahan pada Tahap Training

| Strategi | Tujuan | Detail |
|---|---|---|
| **Data Augmentation** | Menangani imbalance & meningkatkan generalisasi | Rotasi (±20°), flip horizontal, zoom (±15%), shift (±15%) |
| **Class Weight** | Menangani imbalance kelas | Bobot berdasarkan `compute_class_weight('balanced')` |
| **Stratified Split** | Memastikan proporsi kelas sama di setiap split | Rasio 80:10:10 (train:validation:test) |

---

## 10. Kesimpulan

Proses EDA pada dataset penyakit daun jagung menunjukkan bahwa dataset memiliki beberapa karakteristik penting yang perlu diperhatikan:

1. **Dataset tidak seimbang** dengan rasio 2,32x antara kelas terbanyak (Common Rust) dan tersedikit (Gray Leaf Spot)
2. **Proporsi background yang besar** (rata-rata 44,3%) menunjukkan perlunya proses background removal
3. **Kualitas gambar bervariasi** dengan 37,6% gambar memiliki masalah (dominan kontras rendah)
4. **Noise minimal** sehingga tidak menjadi hambatan utama, namun filtering tetap direkomendasikan
5. **Distribusi warna berbeda antar kelas** yang menunjukkan warna sebagai fitur diskriminatif penting

Pipeline preprocessing yang direkomendasikan mencakup: **Background Removal → Adaptive Median Filter → Resize 224×224 → Normalisasi [0,1]**, yang dikombinasikan dengan strategi data augmentation dan class weighting pada tahap training.
