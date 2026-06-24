# Dokumentasi Training Model CNN — Proyek BHUMI

**Proyek**: Perbandingan Arsitektur CNN untuk Deteksi Penyakit Daun Jagung  
**File terkait**:
- `training_mobilenetv3_small_adam.ipynb`
- `training_mobilenetv3_small_sgd.ipynb`
- `training_alexnet_adam.ipynb`
- `training_alexnet_sgd.ipynb`
- `training_vgg16_adam.ipynb`
- `training_vgg16_sgd.ipynb`

---

## Daftar Isi

1. [Gambaran Umum](#1-gambaran-umum)
2. [Daftar Istilah](#2-daftar-istilah)
3. [Struktur Notebook](#3-struktur-notebook)
4. [Penjelasan Per Bagian](#4-penjelasan-per-bagian)
   - [Bagian 1 — Setup & Imports](#bagian-1--setup--imports)
   - [Bagian 2 — Konfigurasi](#bagian-2--konfigurasi)
   - [Bagian 3 — Dataset & DataLoader](#bagian-3--dataset--dataloader)
   - [Bagian 4 — Definisi Model](#bagian-4--definisi-model)
   - [Bagian 5 — Training Utilities](#bagian-5--training-utilities)
   - [Bagian 6 — Phase 1: Feature Extraction](#bagian-6--phase-1-feature-extraction)
   - [Bagian 7 — Phase 2: Fine-tuning](#bagian-7--phase-2-fine-tuning)
   - [Bagian 8 — Evaluasi Test Set](#bagian-8--evaluasi-test-set)
   - [Bagian 9 — Visualisasi](#bagian-9--visualisasi)
   - [Bagian 10 — Simpan Output](#bagian-10--simpan-output)
5. [Perbedaan Antar File Training](#5-perbedaan-antar-file-training)
6. [File Output yang Dihasilkan](#6-file-output-yang-dihasilkan)

---

## 1. Gambaran Umum

Keenam file training ini memiliki **struktur yang identik**. Perbedaannya hanya pada:
- **Arsitektur model** yang digunakan (AlexNet, VGG16, atau MobileNetV3-Small)
- **Optimizer** yang dipakai untuk proses belajar (Adam atau SGD)

Semua file menerapkan pendekatan **transfer learning 2 fase**. Artinya, model tidak dilatih dari nol — melainkan menggunakan model yang sudah pernah dilatih sebelumnya menggunakan jutaan gambar (dari dataset ImageNet), lalu disesuaikan untuk mengenali penyakit daun jagung.

### Mengapa transfer learning?

Ibarat seorang dokter umum yang belajar menjadi dokter spesialis: ia tidak perlu belajar ilmu kedokteran dari nol, cukup memperdalam ilmu di bidang spesialisasinya. Begitu pula model CNN — ia sudah "tahu" cara mengenali tepi, tekstur, dan pola umum dari gambar, tinggal diajarkan untuk mengenali penyakit jagung secara spesifik.

### Dataset yang digunakan

Dataset berisi gambar daun jagung dari 4 kategori:
| Label | Keterangan |
|---|---|
| **Gray Leaf Spot** | Bercak abu-abu pada daun jagung |
| **Common Rust** | Karat/bercak coklat-merah pada daun |
| **Northern Leaf Blight** | Hawar daun dari bagian utara |
| **Healthy** | Daun jagung sehat |

Gambar sudah melalui preprocessing sebelumnya: peningkatan kontras (CLAHE), ukuran diubah ke 224×224 piksel, nilai piksel dinormalisasi ke rentang [0,1].

---

## 2. Daftar Istilah

Berikut penjelasan istilah-istilah teknis yang muncul dalam kode, dalam bahasa yang mudah dipahami:

| Istilah | Penjelasan Sederhana |
|---|---|
| **CNN (Convolutional Neural Network)** | Jenis kecerdasan buatan yang dirancang khusus untuk memproses dan mengenali gambar, mirip cara kerja mata manusia |
| **Model** | "Otak" buatan yang dilatih untuk membuat prediksi. Dalam konteks ini, model memprediksi jenis penyakit daun jagung |
| **Transfer Learning** | Teknik menggunakan model yang sudah dilatih sebelumnya (untuk tugas lain) sebagai titik awal, lalu melatihnya untuk tugas baru. Lebih cepat dan hemat data |
| **Layer (Lapisan)** | Bagian-bagian dalam model yang bekerja seperti filter bertingkat — setiap layer mengenali pola yang semakin kompleks |
| **Epoch** | Satu putaran penuh di mana model belajar dari seluruh data training. Mirip satu kali membaca buku pelajaran dari halaman pertama hingga akhir |
| **Batch** | Sekelompok kecil gambar yang diproses sekaligus dalam satu langkah training. Dalam kode ini, 1 batch = 32 gambar |
| **Loss (Kerugian)** | Angka yang menunjukkan seberapa jauh prediksi model dari jawaban yang benar. Semakin kecil = semakin bagus |
| **Accuracy (Akurasi)** | Persentase prediksi yang benar dari total prediksi. Semakin besar = semakin bagus |
| **Optimizer** | Algoritma yang mengatur cara model "belajar" dari kesalahannya — bagaimana model memperbaiki dirinya setiap langkah |
| **Adam** | Salah satu jenis optimizer yang populer. Pintar menyesuaikan kecepatan belajar secara otomatis untuk setiap parameter |
| **SGD (Stochastic Gradient Descent)** | Jenis optimizer yang lebih klasik dan sederhana. Membutuhkan pengaturan lebih manual tapi sering menghasilkan model yang lebih stabil |
| **Learning Rate (LR)** | Kecepatan belajar model — seberapa besar langkah yang diambil saat memperbaiki diri. Terlalu besar = tidak stabil, terlalu kecil = lambat |
| **Freeze (Membekukan)** | Mengunci bagian model agar tidak berubah saat training. Bagian yang "dibekukan" tidak ikut belajar |
| **Unfreeze (Mencairkan)** | Membuka kunci bagian model yang sebelumnya dibekukan, sehingga bisa ikut belajar kembali |
| **Feature Extraction** | Proses mengekstrak ciri-ciri penting dari gambar (misal: tepi, warna, tekstur) menggunakan lapisan-lapisan model |
| **Fine-tuning** | Proses penyesuaian halus model yang sudah dilatih sebelumnya, agar lebih cocok dengan data/tugas baru |
| **Classifier Head** | Bagian akhir dari model yang mengambil hasil ekstraksi fitur dan memutuskan kelas/kategori gambar |
| **Pretrained Weights** | "Ingatan" hasil belajar model sebelumnya dari dataset lain (ImageNet). Digunakan sebagai titik awal agar tidak belajar dari nol |
| **Validation (Validasi)** | Proses mengecek performa model menggunakan data yang tidak digunakan saat training — mirip latihan ujian |
| **Test Set** | Data ujian sesungguhnya yang hanya digunakan di akhir untuk mengukur performa model secara adil |
| **Checkpoint** | "Foto" kondisi model pada suatu titik training — disimpan agar bisa digunakan kembali nanti |
| **Early Stopping** | Penghentian training otomatis jika model tidak lagi mengalami perbaikan, untuk menghindari pemborosan waktu |
| **Scheduler** | Pengatur kecepatan belajar (LR) — otomatis memperlambat kecepatan belajar jika model mulai stagnan |
| **ReduceLROnPlateau** | Jenis scheduler yang mengurangi LR ketika loss tidak membaik dalam beberapa epoch |
| **Overfitting** | Kondisi di mana model terlalu hafal data training sehingga buruk saat diuji dengan data baru |
| **Confusion Matrix** | Tabel yang menunjukkan secara detail prediksi mana yang benar dan salah, dikelompokkan per kelas |
| **Precision** | Dari semua yang diprediksi sebagai kelas A, berapa yang benar-benar kelas A? |
| **Recall** | Dari semua yang sebenarnya kelas A, berapa yang berhasil dideteksi? |
| **F1-Score** | Nilai rata-rata harmonis dari Precision dan Recall — ukuran keseimbangan keduanya |
| **Macro-average** | Rata-rata metrik dari semua kelas dengan bobot sama, tidak peduli jumlah datanya |
| **GPU/CUDA** | Kartu grafis yang digunakan untuk mempercepat perhitungan training secara masif dan paralel |
| **Random Seed** | Angka awal untuk generator acak — digunakan agar hasil eksperimen bisa direproduksi (hasilnya sama jika dijalankan ulang) |
| **DataLoader** | Komponen yang mengatur proses pengambilan data dalam batch, secara efisien dan bisa paralel |
| **ImageFolder** | Fungsi PyTorch untuk membaca dataset yang disusun dalam folder per kelas |
| **ToTensor** | Fungsi konversi gambar dari format standar (piksel 0–255) ke format yang dimengerti model (angka 0–1) |

---

## 3. Struktur Notebook

Setiap file training terdiri dari **10 bagian** berurutan:

```
1. Setup & Imports          → Memuat semua library yang diperlukan
2. Konfigurasi              → Mengatur semua hyperparameter eksperimen
3. Dataset & DataLoader     → Memuat dan menyiapkan data
4. Definisi Model           → Membangun arsitektur model
5. Training Utilities       → Fungsi-fungsi pembantu training
6. Phase 1 (10 epoch)       → Training: feature extraction (base frozen)
7. Phase 2 (maks 20 epoch)  → Training: fine-tuning (sebagian unfreeze)
8. Evaluasi Test Set        → Pengujian final pada data yang belum pernah dilihat
9. Visualisasi              → Grafik learning curve & confusion matrix
10. Simpan Output           → Menyimpan semua hasil ke file
```

---

## 4. Penjelasan Per Bagian

---

### Bagian 1 — Setup & Imports

**Tujuan**: Memuat semua "alat" (library) yang dibutuhkan sebelum mulai bekerja.

```python
import torch               # Framework utama deep learning
import torch.nn as nn      # Komponen-komponen model neural network
import torch.optim as optim # Optimizer (Adam/SGD)
from torch.utils.data import DataLoader  # Pengambil data secara batch
from torchvision import datasets, transforms  # Dataset & transformasi gambar
import matplotlib.pyplot as plt  # Membuat grafik
import seaborn as sns            # Grafik yang lebih indah (confusion matrix)
from sklearn.metrics import ...  # Menghitung metrik evaluasi
from tqdm import tqdm            # Progress bar saat training
```

Di akhir bagian ini, kode mencetak informasi lingkungan: versi PyTorch, apakah GPU tersedia, dan nama GPU yang digunakan. GPU penting karena training tanpa GPU bisa puluhan kali lebih lambat.

---

### Bagian 2 — Konfigurasi

**Tujuan**: Mengumpulkan semua pengaturan eksperimen di satu tempat agar mudah diubah.

```python
ARCHITECTURE = "mobilenetv3_small"   # Nama arsitektur yang diuji
OPTIMIZER_NAME = "adam"              # Nama optimizer yang dipakai
BATCH_SIZE = 32           # 32 gambar diproses sekaligus per langkah
NUM_CLASSES = 4           # Jumlah kelas: 4 jenis penyakit/sehat
RANDOM_SEED = 42          # Angka awal acak untuk reprodusibilitas

PHASE1_EPOCHS = 10        # Phase 1 berjalan tepat 10 putaran
PHASE2_EPOCHS = 20        # Phase 2 berjalan maksimal 20 putaran
UNFREEZE_LAST_N = 4       # 4 layer terakhir dibuka di Phase 2

LR_PHASE1 = 1e-3          # Kecepatan belajar Phase 1 (= 0.001)
LR_PHASE2 = 1e-4          # Kecepatan belajar Phase 2 (= 0.0001, lebih lambat)

SCHEDULER_FACTOR = 0.5    # LR dikurangi setengah jika stagnan
SCHEDULER_PATIENCE = 3    # Tunggu 3 epoch sebelum kurangi LR
EARLY_STOPPING_PATIENCE = 5  # Hentikan jika 5 epoch tidak ada perbaikan
```

> **Kenapa LR Phase 2 lebih kecil dari Phase 1?**  
> Di Phase 2, model sudah memiliki "pengetahuan" dari ImageNet. Kecepatan belajar yang terlalu besar bisa merusak pengetahuan tersebut. LR kecil memastikan penyesuaian dilakukan secara hati-hati.

> **Kenapa Random Seed = 42?**  
> Nilai ini tidak spesial — hanya konvensi umum di komunitas data science. Yang penting adalah angka yang sama digunakan di semua eksperimen agar hasil bisa dibandingkan secara adil.

**Pengaturan khusus SGD** (hanya di file dengan optimizer SGD):
```python
SGD_MOMENTUM = 0.9        # "Inersia" belajar — membantu SGD lebih stabil
SGD_WEIGHT_DECAY = 1e-4   # Penalti untuk bobot terlalu besar (regularisasi)
```

---

### Bagian 3 — Dataset & DataLoader

**Tujuan**: Memuat gambar dari folder dataset dan menyiapkannya untuk diproses model.

```python
# Transformasi: konversi gambar ke tensor (format yang dimengerti model)
data_transform = transforms.Compose([
    transforms.ToTensor(),   # Gambar PIL [0-255] → Tensor float [0-1]
])
```

> **Mengapa tidak ada normalisasi ImageNet?**  
> Biasanya model pretrained memerlukan normalisasi dengan nilai rata-rata ImageNet. Namun karena gambar di dataset ini sudah dinormalisasi ke [0,1] saat preprocessing, menambah normalisasi ImageNet akan menyebabkan *double normalization* (normalisasi ganda) yang justru merusak data.

```python
# Dataset dibagi menjadi 3 bagian:
train_dataset = datasets.ImageFolder('data/train', ...)  # Untuk belajar
valid_dataset = datasets.ImageFolder('data/valid', ...)  # Untuk evaluasi saat training
test_dataset  = datasets.ImageFolder('data/test',  ...)  # Untuk ujian akhir

# DataLoader mengatur pengambilan data secara efisien
train_loader = DataLoader(train_dataset, batch_size=32, shuffle=True, ...)
# shuffle=True → urutan gambar training diacak setiap epoch (agar tidak hafal urutan)
# shuffle=False → untuk validasi & test, urutan tidak perlu diacak
```

Di akhir bagian ini, kode menampilkan **mapping kelas** — menghubungkan nama folder di dataset dengan nama kelas yang mudah dibaca:

```
[0] Gray Leaf Spot          <- Corn_(maize)___Cercospora_leaf_spot Gray_leaf_spot
[1] Common Rust             <- Corn_(maize)___Common_rust_
[2] Northern Leaf Blight    <- Corn_(maize)___Northern_Leaf_Blight
[3] Healthy                 <- Corn_(maize)___healthy
```

---

### Bagian 4 — Definisi Model

**Tujuan**: Memuat model pretrained dan memodifikasinya untuk kebutuhan proyek ini.

Setiap file menggunakan arsitektur berbeda, namun konsepnya sama:

#### MobileNetV3-Small
```python
model = mobilenet_v3_small(weights=MobileNet_V3_Small_Weights.IMAGENET1K_V1)
# Ganti bagian akhir classifier:
# Aslinya: Linear(1024 → 1000 kelas ImageNet)
# Diganti:  Linear(1024 → 4 kelas penyakit jagung)
model.classifier[-1] = nn.Linear(in_features, NUM_CLASSES)
```

#### AlexNet
```python
model = alexnet(weights=AlexNet_Weights.IMAGENET1K_V1)
# Aslinya: Linear(4096 → 1000 kelas ImageNet)
# Diganti:  Linear(4096 → 4 kelas penyakit jagung)
model.classifier[6] = nn.Linear(4096, NUM_CLASSES)
```

#### VGG16
```python
model = vgg16(weights=VGG16_Weights.IMAGENET1K_V1)
# Aslinya: Linear(4096 → 1000 kelas ImageNet)
# Diganti:  Linear(4096 → 4 kelas penyakit jagung)
model.classifier[6] = nn.Linear(4096, NUM_CLASSES)
```

**Fungsi-fungsi penting**:

```python
def freeze_base_layers(model):
    """Membekukan seluruh bagian 'features' agar tidak ikut belajar di Phase 1."""
    for param in model.features.parameters():
        param.requires_grad = False  # requires_grad=False artinya tidak boleh diubah
    for param in model.classifier.parameters():
        param.requires_grad = True   # classifier boleh diubah (dilatih)

def unfreeze_last_n_layers(model, n=4):
    """Membuka kunci N layer terakhir dari bagian features untuk Phase 2."""
    children = list(model.features.children())
    for child in children[-n:]:      # ambil N layer dari belakang
        for param in child.parameters():
            param.requires_grad = True  # buka kuncinya
```

> **Analogi freeze/unfreeze**:  
> Bayangkan model seperti sebuah tim 10 orang. Di Phase 1, hanya 1 orang (classifier) yang diizinkan berlatih — sisanya "tidur". Di Phase 2, 4 orang terakhir juga dibangunkan untuk ikut berlatih bersama.

---

### Bagian 5 — Training Utilities

**Tujuan**: Mendefinisikan fungsi-fungsi pembantu yang digunakan di kedua fase training.

#### Fungsi `train_one_epoch` — Proses Belajar Satu Putaran

```python
def train_one_epoch(model, loader, criterion, optimizer, device):
    model.train()   # Aktifkan mode training (dropout dan batchnorm aktif)
    
    for images, labels in loader:
        optimizer.zero_grad()       # Hapus sisa perhitungan sebelumnya
        outputs = model(images)     # Model membuat prediksi
        loss = criterion(outputs, labels)  # Hitung seberapa salah prediksi
        loss.backward()             # Hitung koreksi yang perlu dilakukan
        optimizer.step()            # Terapkan koreksi ke model
```

Setiap langkah dalam satu epoch:
1. Model melihat 32 gambar (1 batch)
2. Model membuat prediksi kelas untuk tiap gambar
3. Loss dihitung — seberapa jauh prediksi dari jawaban benar
4. Model memperbaiki dirinya berdasarkan loss tersebut
5. Ulangi sampai semua gambar selesai diproses

#### Fungsi `validate` — Pengecekan Performa

```python
def validate(model, loader, criterion, device):
    model.eval()   # Mode evaluasi (dropout nonaktif, tidak ada update bobot)
    
    with torch.no_grad():  # Tidak perlu hitung gradien saat validasi
        for images, labels in loader:
            outputs = model(images)
            # Kumpulkan prediksi dan label asli
```

> **Perbedaan `model.train()` vs `model.eval()`**:  
> Di mode training, beberapa komponen model (seperti dropout) aktif untuk mencegah model terlalu hafal. Di mode evaluasi, semua komponen dinonaktifkan agar prediksi lebih konsisten.

#### Kelas `EarlyStopping` — Penghenti Otomatis

```python
class EarlyStopping:
    def __init__(self, patience=5):
        self.patience = patience    # Tunggu 5 epoch sebelum menyerah
        self.counter = 0            # Penghitung epoch tanpa perbaikan
        self.best_loss = None       # Loss terbaik yang pernah dicapai
    
    def __call__(self, val_loss):
        if val_loss < self.best_loss:  # Ada perbaikan?
            self.best_loss = val_loss
            self.counter = 0           # Reset penghitung
        else:
            self.counter += 1          # Tambah penghitung
            if self.counter >= self.patience:
                return True  # Hentikan training!
        return False  # Lanjutkan training
```

**Cara kerja Early Stopping step-by-step**:
```
Epoch 1 → val_loss: 0.80 → best! counter=0
Epoch 2 → val_loss: 0.65 → best! counter=0
Epoch 3 → val_loss: 0.70 → tidak membaik, counter=1
Epoch 4 → val_loss: 0.72 → tidak membaik, counter=2
Epoch 5 → val_loss: 0.60 → best! counter=0  ← diselamatkan!
Epoch 6 → val_loss: 0.63 → tidak membaik, counter=1
Epoch 7 → val_loss: 0.65 → tidak membaik, counter=2
Epoch 8 → val_loss: 0.67 → tidak membaik, counter=3
Epoch 9 → val_loss: 0.64 → tidak membaik, counter=4
Epoch 10→ val_loss: 0.66 → tidak membaik, counter=5 → BERHENTI!
```

**Loss function** yang digunakan adalah `CrossEntropyLoss` — fungsi standar untuk masalah klasifikasi multi-kelas. Semakin dekat prediksi dengan label yang benar, semakin kecil nilai loss-nya.

---

### Bagian 6 — Phase 1: Feature Extraction

**Tujuan**: Melatih hanya bagian classifier baru (kepala model) sambil membekukan seluruh bagian feature extractor (badan model).

```
Kondisi Phase 1:
├── Base model (features): DIBEKUKAN — tidak berubah
└── Classifier (kepala) : AKTIF — dilatih
```

**Alur Phase 1**:
1. `freeze_base_layers(model)` — bekukan bagian features
2. Buat optimizer Adam/SGD hanya untuk parameter yang aktif
3. Buat scheduler `ReduceLROnPlateau` untuk menyesuaikan LR
4. Jalankan loop training selama **10 epoch**
5. Setiap epoch: training → validasi → update scheduler
6. Simpan checkpoint model terbaik berdasarkan `val_loss` terkecil

```python
for epoch in range(1, PHASE1_EPOCHS + 1):
    train_loss, train_acc = train_one_epoch(...)
    val_loss, val_acc, val_f1 = validate(...)
    scheduler.step(val_loss)   # Sesuaikan LR jika perlu
    
    if val_loss < best_val_loss_p1:
        # Ini epoch terbaik sejauh ini, simpan modelnya!
        torch.save(model.state_dict(), 'best_model_phase1.pth')
```

> **Mengapa hanya 10 epoch di Phase 1?**  
> Karena hanya classifier kecil yang dilatih (hanya ~1% parameter), model konvergen dengan cepat. Terlalu banyak epoch justru bisa menyebabkan overfitting pada bagian classifier.

---

### Bagian 7 — Phase 2: Fine-tuning

**Tujuan**: Menyempurnakan model lebih lanjut dengan membuka beberapa layer teratas feature extractor.

**Alur Phase 2**:
1. Load bobot model terbaik dari Phase 1
2. `unfreeze_last_n_layers(model, 4)` — buka 4 layer terakhir
3. Buat optimizer baru dengan LR lebih kecil (`1e-4`)
4. Buat scheduler dan EarlyStopping baru
5. Jalankan loop training **maksimal 20 epoch**
6. Setiap epoch: training → validasi → update scheduler → cek early stopping
7. Simpan checkpoint model terbaik berdasarkan `val_loss` terkecil

```
Kondisi Phase 2:
├── Base model — layer awal   : MASIH DIBEKUKAN
├── Base model — 4 layer akhir: DIBUKA — ikut dilatih
└── Classifier (kepala)       : AKTIF — terus dilatih
```

**Kapan Phase 2 berhenti?**
- **Skenario 1**: Berjalan penuh hingga epoch ke-20
- **Skenario 2**: Berhenti lebih awal jika `val_loss` tidak membaik selama 5 epoch berturut-turut (Early Stopping)

```python
for epoch in range(1, PHASE2_EPOCHS + 1):
    ...
    if early_stopping(val_loss):   # Cek kondisi berhenti
        print(f"Early stopping triggered di epoch {epoch}")
        break  # Hentikan loop!
```

---

### Bagian 8 — Evaluasi Test Set

**Tujuan**: Mengukur performa model final secara objektif menggunakan data yang belum pernah dilihat selama training maupun validasi.

> **Mengapa harus ada test set terpisah?**  
> Training set digunakan untuk belajar. Validation set digunakan untuk evaluasi saat training (sehingga model secara tidak langsung "disesuaikan" dengan data validasi). Test set adalah data yang benar-benar baru — digunakan hanya sekali di akhir untuk mengukur kemampuan model yang sesungguhnya.

```python
# Load model terbaik dari Phase 2
model.load_state_dict(torch.load('best_model_phase2.pth'))
model.eval()

# Jalankan inferensi (prediksi) tanpa update bobot
with torch.no_grad():
    for images, labels in test_loader:
        outputs = model(images)
        _, predicted = outputs.max(1)  # Ambil kelas dengan skor tertinggi
```

**Metrik yang dihitung**:

| Metrik | Rumus Sederhana | Arti |
|---|---|---|
| **Accuracy** | Prediksi benar / Total prediksi | Berapa persen yang benar secara keseluruhan |
| **Precision** | Benar positif / (Benar positif + Salah positif) | Dari yang diprediksi kelas X, berapa yang benar X? |
| **Recall** | Benar positif / (Benar positif + Salah negatif) | Dari semua yang sebenarnya X, berapa yang terdeteksi? |
| **F1-Score** | 2 × (Precision × Recall) / (Precision + Recall) | Keseimbangan antara Precision dan Recall |
| **Macro-average** | Rata-rata dari semua kelas | Bobot setiap kelas sama, tidak terpengaruh jumlah data |

Hasil evaluasi disimpan dalam dua file:
- `classification_report.txt` — laporan teks lengkap dari sklearn
- `evaluation_results.json` — data JSON terstruktur untuk analisis lebih lanjut

---

### Bagian 9 — Visualisasi

**Tujuan**: Membuat grafik untuk memudahkan interpretasi hasil training dan evaluasi.

#### Confusion Matrix

Tabel yang menunjukkan prediksi model vs kenyataan:

```
                Prediksi
              GLS   CR   NLB   H
Aktual GLS  [ 95    2     1    2 ]
       CR   [  1   98     1    0 ]
       NLB  [  3    1    93    3 ]
       H    [  0    0     2   98 ]
```

- Nilai di **diagonal** (kiri atas ke kanan bawah) = prediksi benar
- Nilai di **luar diagonal** = kesalahan prediksi

Dibuat dalam 2 versi:
- **Raw** — nilai absolut (jumlah gambar)
- **Normalized** — persentase per baris (memudahkan perbandingan antar kelas dengan jumlah berbeda)

#### Training Curves (Grafik Pembelajaran)

Grafik 2×2 yang menunjukkan perjalanan training:

```
┌─────────────────┬─────────────────┐
│ Phase 1 - Loss  │ Phase 1 - Acc   │
├─────────────────┼─────────────────┤
│ Phase 2 - Loss  │ Phase 2 - Acc   │
└─────────────────┴─────────────────┘
```

Setiap grafik menampilkan kurva training (biru) dan validasi (merah). Grafik yang baik menunjukkan:
- Kedua kurva turun (untuk loss) atau naik (untuk accuracy)
- Jarak antara kedua kurva tidak terlalu jauh (tidak overfitting)

---

### Bagian 10 — Simpan Output

**Tujuan**: Menyimpan semua hasil eksperimen ke file agar bisa dianalisis dan dibandingkan kemudian.

```python
# Simpan history training (loss & accuracy setiap epoch)
history = {
    'phase1': phase1_history,   # List 10 epoch Phase 1
    'phase2': phase2_history,   # List N epoch Phase 2 (bisa < 20 jika early stop)
}
with open('training_history.json', 'w') as f:
    json.dump(history, f)
```

**File yang dihasilkan** (lihat juga bagian berikutnya):
- `best_model_phase1.pth` — bobot model terbaik dari Phase 1
- `best_model_phase2.pth` — bobot model terbaik dari Phase 2
- `training_history.json` — riwayat loss & accuracy tiap epoch
- `evaluation_results.json` — hasil evaluasi final (accuracy, F1, dll)
- `classification_report.txt` — laporan evaluasi lengkap
- `confusion_matrix_raw.png` — grafik confusion matrix nilai absolut
- `confusion_matrix_normalized.png` — grafik confusion matrix persentase
- `training_curves.png` — grafik kurva training

---

## 5. Perbedaan Antar File Training

Semua file menggunakan struktur yang sama. Perbedaannya:

### Perbedaan Arsitektur Model

| File | Arsitektur | Jumlah Parameter | Karakteristik |
|---|---|---|---|
| `training_mobilenetv3_small_*.ipynb` | MobileNetV3-Small | ~2.5 juta | Sangat ringan, cocok untuk perangkat terbatas |
| `training_alexnet_*.ipynb` | AlexNet | ~61 juta | Arsitektur klasik (2012), relatif sederhana |
| `training_vgg16_*.ipynb` | VGG16 | ~138 juta | Arsitektur dalam & akurat, tapi berat |

### Perbedaan Optimizer

| Optimizer | LR Phase 1 | LR Phase 2 | Parameter Tambahan |
|---|---|---|---|
| **Adam** | `1e-3` (0.001) | `1e-4` (0.0001) | Tidak ada |
| **SGD** | `1e-2` (0.01) | `1e-3` (0.001) | momentum=0.9, weight_decay=1e-4 |

> **Adam vs SGD**:  
> Adam lebih adaptif — ia secara otomatis menyesuaikan kecepatan belajar per parameter. SGD lebih sederhana namun membutuhkan pengaturan lebih manual. Dalam eksperimen ini, SGD menggunakan LR yang lebih besar karena SGD membutuhkan "dorongan" lebih kuat dibanding Adam.

---

## 6. File Output yang Dihasilkan

Setiap eksperimen menghasilkan folder output tersendiri di `/kaggle/working/{nama_eksperimen}/`:

```
mobilenetv3_small_adam/
├── best_model_phase1.pth          # Bobot model terbaik Phase 1
├── best_model_phase2.pth          # Bobot model terbaik Phase 2
├── training_history.json          # Riwayat loss & accuracy per epoch
├── evaluation_results.json        # Hasil evaluasi final (JSON)
├── classification_report.txt      # Laporan evaluasi (teks)
├── confusion_matrix_raw.png       # Confusion matrix (absolut)
├── confusion_matrix_normalized.png # Confusion matrix (persentase)
└── training_curves.png            # Kurva training
```

Format `evaluation_results.json`:
```json
{
  "architecture": "mobilenetv3_small",
  "optimizer": "adam",
  "test_accuracy": 0.9523,
  "test_loss": 0.1234,
  "test_precision_macro": 0.9534,
  "test_recall_macro": 0.9512,
  "test_f1_macro": 0.9521,
  "per_class": {
    "Gray Leaf Spot": {"precision": 0.95, "recall": 0.94, "f1": 0.945, "support": 100},
    "Common Rust":    {"precision": 0.97, "recall": 0.96, "f1": 0.965, "support": 100},
    ...
  },
  "training_time_phase1_seconds": 245.3,
  "training_time_phase2_seconds": 512.7,
  "training_time_total_seconds": 758.0
}
```

---

*Dokumentasi ini dibuat untuk Proyek BHUMI — Skripsi Perbandingan Arsitektur CNN untuk Deteksi Penyakit Daun Jagung.*
