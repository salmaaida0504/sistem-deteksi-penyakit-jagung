"""
FastAPI Inference Service — Deteksi Penyakit Daun Jagung
========================================================
Service ini memuat model MobileNetV3-Small yang sudah di-training
(mobilenetv3_small_sgd_best_model_phase2.pth) dan menyediakan endpoint REST API untuk
melakukan prediksi penyakit daun jagung.

Arsitektur : MobileNetV3-Small (Transfer Learning)
Optimizer  : SGD (saat training)
Kelas      : 4 (Cercospora, Common Rust, Northern Leaf Blight, Healthy)
Port       : 8001
"""

import io
import os
import sys
import logging
from pathlib import Path
import cv2
import numpy as np
import torch
import torch.nn as nn
from torchvision.models import mobilenet_v3_small, MobileNet_V3_Small_Weights
from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import uvicorn

# ============================================================
# Konfigurasi Logging
# ============================================================
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[logging.StreamHandler(sys.stdout)],
)
logger = logging.getLogger(__name__)

# ============================================================
# Konstanta & Konfigurasi
# ============================================================

# Path ke file model (relatif terhadap lokasi script ini)
MODEL_PATH = Path(__file__).parent / "mobilenetv3_small_sgd_best_model_phase2.pth"

# Jumlah kelas dan mapping index → nama kelas
NUM_CLASSES = 4
CLASS_NAMES = {
    0: "Cercospora",
    1: "Common_Rust",
    2: "Northern_Leaf_Blight",
    3: "Healthy",
}

# Mapping nama kelas → nama tampilan (user-friendly)
CLASS_DISPLAY_NAMES = {
    "Cercospora": "Gray Leaf Spot (Cercospora)",
    "Common_Rust": "Common Rust (Karat Jagung)",
    "Northern_Leaf_Blight": "Northern Leaf Blight (Hawar Daun)",
    "Healthy": "Healthy (Sehat)",
}

# Parameter preprocessing (identik dengan saat training)
TARGET_SIZE = (224, 224)
CLAHE_CLIP_LIMIT = 2.0
CLAHE_TILE_GRID = (8, 8)

# Server
HOST = "0.0.0.0"
PORT = 8001


# ============================================================
# Fungsi Preprocessing (identik dengan pipeline training)
# ============================================================

def apply_clahe(image: np.ndarray) -> np.ndarray:
    """
    Meningkatkan kontras gambar menggunakan CLAHE pada channel L
    di ruang warna Lab. Identik dengan preprocessing saat training.
    """
    lab = cv2.cvtColor(image, cv2.COLOR_BGR2Lab)
    l_channel, a_channel, b_channel = cv2.split(lab)

    clahe = cv2.createCLAHE(
        clipLimit=CLAHE_CLIP_LIMIT,
        tileGridSize=CLAHE_TILE_GRID,
    )
    l_enhanced = clahe.apply(l_channel)

    lab_enhanced = cv2.merge([l_enhanced, a_channel, b_channel])
    result = cv2.cvtColor(lab_enhanced, cv2.COLOR_Lab2BGR)
    return result


def preprocess_image(image_bytes: bytes) -> torch.Tensor:
    """
    Menjalankan pipeline preprocessing yang IDENTIK dengan saat training:
    1. Decode gambar dari bytes
    2. CLAHE (peningkatan kontras adaptif)
    3. Resize ke 224×224 (INTER_LANCZOS4)
    4. Normalisasi ke [0, 1] (float32)
    5. Konversi ke tensor PyTorch (C, H, W)

    Args:
        image_bytes: Raw bytes dari file gambar yang diupload

    Returns:
        torch.Tensor: Tensor siap inferensi dengan shape (1, 3, 224, 224)
    """
    # Decode gambar dari bytes
    nparr = np.frombuffer(image_bytes, np.uint8)
    image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

    if image is None:
        raise ValueError("Gambar tidak dapat di-decode. Pastikan format file valid (JPG/PNG).")

    # Step 1: CLAHE
    image = apply_clahe(image)

    # Step 2: Resize ke 224×224
    image = cv2.resize(image, TARGET_SIZE, interpolation=cv2.INTER_LANCZOS4)

    # Step 3: Normalisasi ke [0, 1]
    image = image.astype(np.float32) / 255.0

    # Step 4: Konversi BGR → RGB (OpenCV menggunakan BGR, PyTorch menggunakan RGB)
    image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)

    # Step 5: Konversi ke tensor (H, W, C) → (C, H, W) lalu tambah batch dimension
    tensor = torch.from_numpy(image).permute(2, 0, 1).unsqueeze(0)

    return tensor


# ============================================================
# Fungsi Load Model
# ============================================================

def load_model(model_path: Path) -> nn.Module:
    """
    Memuat model MobileNetV3-Small dengan classifier head yang dimodifikasi
    untuk 4 kelas (identik dengan arsitektur saat training).

    Args:
        model_path: Path ke file .pth

    Returns:
        nn.Module: Model siap inferensi dalam mode eval
    """
    logger.info(f"Memuat model dari: {model_path}")

    if not model_path.exists():
        raise FileNotFoundError(
            f"File model tidak ditemukan: {model_path}\n"
            f"Pastikan file 'mobilenetv3_small_sgd_best_model_phase2.pth' ada di folder 'python/'."
        )

    # Buat arsitektur model (tanpa pretrained weights)
    model = mobilenet_v3_small(weights=None)

    # Modifikasi classifier head: 1024 → 4 kelas (identik dengan training)
    in_features = model.classifier[-1].in_features
    model.classifier[-1] = nn.Linear(in_features, NUM_CLASSES)

    # Muat bobot model yang sudah di-training
    device = torch.device("cuda" if torch.cuda.is_available() else "cpu")
    state_dict = torch.load(model_path, map_location=device, weights_only=True)
    model.load_state_dict(state_dict)

    # Set ke mode evaluasi (nonaktifkan dropout, batch norm dalam mode inference)
    model.eval()
    model.to(device)

    logger.info(f"Model berhasil dimuat pada device: {device}")
    logger.info(f"Jumlah parameter: {sum(p.numel() for p in model.parameters()):,}")

    return model


# ============================================================
# Inisialisasi FastAPI App & Model
# ============================================================

app = FastAPI(
    title="BHUMI — API Deteksi Penyakit Daun Jagung",
    description="REST API untuk deteksi penyakit daun jagung menggunakan model CNN MobileNetV3-Small.",
    version="1.0.0",
)

# CORS — izinkan akses dari Laravel (localhost)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Load model saat startup
model = None
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")


@app.on_event("startup")
async def startup_event():
    """Load model saat server pertama kali dijalankan."""
    global model
    try:
        model = load_model(MODEL_PATH)
        logger.info("[OK] Server siap menerima request prediksi.")
    except Exception as e:
        logger.error(f"[ERROR] Gagal memuat model: {e}")
        raise


# ============================================================
# API Endpoints
# ============================================================

@app.get("/")
async def root():
    """Health check endpoint."""
    return {
        "status": "running",
        "service": "BHUMI — Deteksi Penyakit Daun Jagung",
        "model": "MobileNetV3-Small (Phase 2 Fine-tuned)",
        "classes": list(CLASS_NAMES.values()),
        "device": str(device),
    }


@app.get("/health")
async def health_check():
    """Endpoint untuk mengecek apakah model sudah dimuat."""
    return {
        "status": "healthy" if model is not None else "unhealthy",
        "model_loaded": model is not None,
        "device": str(device),
    }


@app.post("/predict")
async def predict(file: UploadFile = File(...)):
    """
    Endpoint utama untuk prediksi penyakit daun jagung.

    Menerima file gambar (JPG/PNG), menjalankan preprocessing + inferensi,
    dan mengembalikan probabilitas per kelas beserta prediksi utama.

    Args:
        file: File gambar yang diupload (multipart/form-data)

    Returns:
        JSON dengan prediksi kelas, confidence, dan probabilitas per kelas
    """
    if model is None:
        raise HTTPException(status_code=503, detail="Model belum dimuat. Coba lagi nanti.")

    # Validasi tipe file
    content_type = file.content_type or ""
    if not content_type.startswith("image/"):
        raise HTTPException(
            status_code=400,
            detail=f"Tipe file tidak valid: {content_type}. Hanya menerima gambar (JPG/PNG).",
        )

    try:
        # Baca file yang diupload
        image_bytes = await file.read()

        if len(image_bytes) == 0:
            raise HTTPException(status_code=400, detail="File gambar kosong.")

        # Preprocessing (identik dengan pipeline training)
        tensor = preprocess_image(image_bytes)
        tensor = tensor.to(device)

        # Inferensi (prediksi)
        with torch.no_grad():
            outputs = model(tensor)
            probabilities = torch.softmax(outputs, dim=1)
            confidence, predicted_idx = torch.max(probabilities, dim=1)

        # Konversi ke Python types
        predicted_class = CLASS_NAMES[predicted_idx.item()]
        confidence_value = round(confidence.item() * 100, 2)

        # Probabilitas per kelas (dalam persen)
        all_predictions = {}
        for idx, class_name in CLASS_NAMES.items():
            all_predictions[class_name] = round(probabilities[0][idx].item() * 100, 2)

        logger.info(
            f"Prediksi: {predicted_class} ({confidence_value}%) | "
            f"File: {file.filename}"
        )

        return {
            "success": True,
            "predicted_class": predicted_class,
            "predicted_display_name": CLASS_DISPLAY_NAMES.get(predicted_class, predicted_class),
            "confidence": confidence_value,
            "all_predictions": all_predictions,
        }

    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e))
    except Exception as e:
        logger.error(f"Error saat prediksi: {e}", exc_info=True)
        raise HTTPException(status_code=500, detail=f"Terjadi kesalahan saat memproses gambar: {str(e)}")


# ============================================================
# Entry Point
# ============================================================

if __name__ == "__main__":
    print("=" * 60)
    print("  BHUMI — FastAPI Inference Server")
    print(f"  Model   : MobileNetV3-Small (mobilenetv3_small_sgd_best_model_phase2.pth)")
    print(f"  Port    : {PORT}")
    print(f"  Device  : {device}")
    print("=" * 60)

    uvicorn.run(
        "predict_service:app",
        host=HOST,
        port=PORT,
        reload=False,
        log_level="info",
    )
