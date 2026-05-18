import os
from ultralytics import YOLO

# ==========================================
# 🔥 SETUP PATH (ANTI ERROR)
# ==========================================

# Folder tempat file ini berada (backend/test/)
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Naik 1 level ke backend/
BACKEND_DIR = os.path.abspath(os.path.join(BASE_DIR, ".."))

# Path ke model (FIXED)
MODEL_PATH = os.path.join(
    BACKEND_DIR,
    "runs",
    "detect",
    "currency_lens_v3_s",
    "weights",
    "best.pt"
)

# Path folder output hasil test
OUTPUT_DIR = os.path.join(BASE_DIR, "results")
os.makedirs(OUTPUT_DIR, exist_ok=True)

# ==========================================
# 🔍 DEBUG PATH & PREPARATION
# ==========================================

print("📁 BASE DIR:", BASE_DIR)
print("📁 BACKEND DIR:", BACKEND_DIR)
print("🔥 MODEL PATH:", MODEL_PATH)
print("📁 OUTPUT RESULTS DIR:", OUTPUT_DIR)

# Check model file
if not os.path.exists(MODEL_PATH):
    print("❌ ERROR: MODEL TIDAK DITEMUKAN!")
    exit()

# Cari semua file gambar di dalam folder test/
VALID_EXTENSIONS = ('.jpg', '.jpeg', '.png', '.bmp', '.webp')
test_images = [
    f for f in os.listdir(BASE_DIR)
    if f.lower().endswith(VALID_EXTENSIONS)
]

if len(test_images) == 0:
    print(f"❌ ERROR: Tidak ada gambar uji (.jpg, .jpeg, .png) di folder: {BASE_DIR}")
    exit()

print(f"\n📸 Ditemukan {len(test_images)} gambar untuk diuji secara otomatis:")
for img_name in test_images:
    print(f"  - {img_name}")

# ==========================================
# 🚀 LOAD MODEL
# ==========================================

print("\n🚀 Loading model...")
model = YOLO(MODEL_PATH)

# ==========================================
# 🧪 RUN AUTO DETECTION FOR ALL IMAGES
# ==========================================

print("\n🧪 Running automatic batch detection...")

# Kita gunakan parameter YOLO save=True dengan project & name terarah
# Agar hasil langsung disimpan di backend/test/results/
results = model(
    [os.path.join(BASE_DIR, img) for img in test_images],
    conf=0.2,   # Confidence threshold standard
    iou=0.5,
    save=True,  # Auto save hasil visualisasi
    project=BASE_DIR,
    name="results",
    exist_ok=True # Overwrite/simpan di folder yang sama tanpa membuat folder baru
)

# ==========================================
# 📊 SUMMARY OUTPUT
# ==========================================

print("\n" + "="*40)
print("📊 RINGKASAN BATCH DETEKSI:")
print("="*40)

for idx, res in enumerate(results):
    img_name = test_images[idx]
    boxes = res.boxes
    if boxes is None or len(boxes) == 0:
        print(f"❌ {img_name} : TIDAK ADA DETEKSI")
    else:
        print(f"✅ {img_name} : Terdeteksi {len(boxes)} objek:")
        for b_idx, box in enumerate(boxes):
            cls = int(box.cls[0])
            conf = float(box.conf[0])
            name = model.names[cls]
            print(f"   → [{b_idx+1}] {name} (confidence={round(conf, 3)})")

print("\n" + "="*40)
print("📁 Uji selesai! Silakan cek semua hasil visualisasi gambar di folder:")
print(f"👉 {OUTPUT_DIR}")
print("="*40)