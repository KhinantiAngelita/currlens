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

# Path ke gambar test
TEST_IMAGE = os.path.join(BASE_DIR, "tesr.jpeg")

# ==========================================
# 🔍 DEBUG PATH
# ==========================================

print("📁 BASE DIR:", BASE_DIR)
print("📁 BACKEND DIR:", BACKEND_DIR)
print("🔥 MODEL PATH:", MODEL_PATH)
print("🖼️ TEST IMAGE:", TEST_IMAGE)

# ==========================================
# ❌ CHECK FILE
# ==========================================

if not os.path.exists(MODEL_PATH):
    print("❌ ERROR: MODEL TIDAK DITEMUKAN!")
    exit()

if not os.path.exists(TEST_IMAGE):
    print("❌ ERROR: test.jpg tidak ditemukan di folder test/")
    exit()

# ==========================================
# 🚀 LOAD MODEL
# ==========================================

print("\n🚀 Loading model...")
model = YOLO(MODEL_PATH)

# ==========================================
# 🧠 PRINT CLASSES
# ==========================================

print("\n📋 Model Classes:")
for i, name in model.names.items():
    print(f"{i}: {name}")

# ==========================================
# 🧪 RUN DETECTION
# ==========================================

print("\n🧪 Running detection...")

results = model(
    TEST_IMAGE,
    conf=0.05,   # 🔥 super low for debug
    iou=0.5,
    save=True    # 🔥 auto save hasil
)

# ==========================================
# 📊 DEBUG OUTPUT
# ==========================================

print("\n📊 RAW RESULT:")
boxes = results[0].boxes

if boxes is None or len(boxes) == 0:
    print("❌ NO DETECTION")
else:
    print(f"✅ DETECTED {len(boxes)} OBJECT(S)\n")

    for i, box in enumerate(boxes):
        cls = int(box.cls[0])
        conf = float(box.conf[0])
        name = model.names[cls]

        print(f"[{i}] {name} | confidence={round(conf, 3)}")

# ==========================================
# 📂 INFO OUTPUT FILE
# ==========================================

print("\n📁 Check hasil di folder:")
print("👉 runs/detect/predict/")