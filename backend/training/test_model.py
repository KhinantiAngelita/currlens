import os
from ultralytics import YOLO
import torch

# ==========================================
# GOAL 5: STANDALONE INFERENCE TEST
# ==========================================

MODEL_PATH = "runs/detect/currency_lens_v3_s/weights/best.pt"

if not os.path.exists(MODEL_PATH):
    print(f"❌ ERROR: Model not found at {MODEL_PATH}")
    exit()

print(f"🚀 Loading model: {MODEL_PATH}")
model = YOLO(MODEL_PATH)

# Verify Classes
print("\n📋 Model Classes:")
print(model.names)

# Test Image
# Check if test.jpg exists, if not use any jpg in current folder
test_image = "test.jpg"
if not os.path.exists(test_image):
    # Try to find any jpg
    jpgs = [f for f in os.listdir('.') if f.lower().endswith('.jpg') or f.lower().endswith('.png')]
    if jpgs:
        test_image = jpgs[0]
        print(f"⚠️ test.jpg not found, using {test_image} instead.")
    else:
        print("❌ ERROR: No images found in directory to test.")
        exit()

print(f"📸 Running inference on: {test_image}")
results = model(test_image, conf=0.05, save=True)

# Print Summary
print("\n🔍 Detection Summary:")
for result in results:
    boxes = result.boxes
    if len(boxes) == 0:
        print("  ❌ No objects detected even at conf=0.05")
    else:
        print(f"  ✅ {len(boxes)} objects detected!")
        for i, box in enumerate(boxes):
            cls = int(box.cls[0])
            conf = float(box.conf[0])
            print(f"     - [{i}] {model.names[cls]} | Conf: {round(conf, 4)}")

print("\n📂 Visual results saved in 'runs/detect/predict/'")
