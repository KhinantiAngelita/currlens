import os
import glob
from ultralytics import YOLO

# ==========================================
# 🔥 SETUP PATHS
# ==========================================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
BACKEND_DIR = os.path.abspath(os.path.join(BASE_DIR, ".."))

# List model yang potensial (sama seperti di app.py)
MODEL_PATHS = {
    "currency_lens_v3_s": os.path.join(BACKEND_DIR, "runs", "detect", "currency_lens_v3_s", "weights", "best.pt"),
    "multi_currency_gpu_v2": os.path.join(BACKEND_DIR, "runs", "detect", "multi_currency_gpu_v2", "weights", "best.pt"),
    "currency_lens_v3_finetune-2": os.path.join(BACKEND_DIR, "runs", "detect", "currency_lens_v3_finetune-2", "weights", "best.pt"),
}

# ==========================================
# 🔍 CARI MODEL AKTIF
# ==========================================
active_models = {}
for name, path in MODEL_PATHS.items():
    if os.path.exists(path):
        print(f"🔥 Menemukan model aktif: {name}")
        active_models[name] = YOLO(path)

if not active_models:
    print("❌ ERROR: Tidak ada file model 'best.pt' yang ditemukan di folder runs/detect/!")
    print("Pastikan minimal ada satu model di runs/detect/currency_lens_v3_s/weights/best.pt")
    exit()

# ==========================================
# 🖼️ CARI SEMUA GAMBAR DI FOLDER TEST
# ==========================================
EXTENSIONS = ("*.jpg", "*.jpeg", "*.png", "*.webp", "*.bmp")
test_images = []
for ext in EXTENSIONS:
    # Cari di folder test/ secara case-insensitive
    test_images.extend(glob.glob(os.path.join(BASE_DIR, ext)))
    test_images.extend(glob.glob(os.path.join(BASE_DIR, ext.upper())))

# Hapus duplikat path jika ada
test_images = list(set(test_images))

if not test_images:
    print(f"❌ ERROR: Tidak ada file gambar ({', '.join(EXTENSIONS)}) di folder test/!")
    exit()

print(f"🖼️ Menemukan {len(test_images)} gambar untuk diuji.")

# ==========================================
# 🚀 JALANKAN DETEKSI OTOMATIS
# ==========================================
for img_path in test_images:
    img_name = os.path.basename(img_path)
    print("\n" + "="*60)
    print(f"🖼️ MENGUJI GAMBAR: {img_name}")
    print("="*60)

    for model_name, model in active_models.items():
        print(f"\n🧠 Model: {model_name}")
        print("🧪 Running detection...")

        results = model(
            img_path,
            conf=0.15,   # Confidence threshold untuk test
            iou=0.5,
            save=True    # Menyimpan hasil visualisasi (.jpg) secara otomatis
        )

        # Print Ringkasan Deteksi
        boxes = results[0].boxes
        if boxes is None or len(boxes) == 0:
            print("   ↳ ❌ TIDAK ADA DETEKSI")
        else:
            print(f"   ↳ ✅ TERDETEKSI {len(boxes)} OBJEK:")
            for idx, box in enumerate(boxes):
                cls = int(box.cls[0])
                conf = float(box.conf[0])
                name = model.names[cls]
                print(f"     [{idx+1}] {name} | Confidence: {round(conf, 3)}")

# ==========================================
# 📂 INFO LOKASI OUTPUT
# ==========================================
print("\n" + "="*60)
print("✅ SEMUA DETEKSI OTOMATIS SELESAI!")
print("📂 Hasil visualisasi gambar (.jpg) disimpan secara otomatis di:")
print("👉 backend/runs/detect/predict/, predict2, dst.")
print("="*60)