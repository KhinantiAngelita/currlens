from ultralytics import YOLO
import os
import torch
import multiprocessing
from PIL import Image
import matplotlib.pyplot as plt


def main():

    # ==============================
    # 1. FORCE GPU
    # ==============================
    print("\n🔍 Checking GPU...")

    if not torch.cuda.is_available():
        raise RuntimeError("❌ GPU tidak terdeteksi! Training dibatalkan.")

    device = 0
    print(f"🔥 GPU aktif: {torch.cuda.get_device_name(0)}")

    # ==============================
    # 2. CEK YAML
    # ==============================
    data_yaml = "multi_currency.yml"

    if not os.path.exists(data_yaml):
        print("❌ YAML tidak ditemukan!")
        return

    print("✅ YAML ditemukan")

    # ==============================
    # 3. LOAD MODEL (PRETRAINED)
    # ==============================
    print("\n🚀 Loading model...")
    model = YOLO("yolov8n.pt")

    # ==============================
    # 4. TRAINING (GPU + EARLY STOP)
    # ==============================
    print("\n🚀 Training dimulai (GPU MODE)...\n")

    model.train(
        data=data_yaml,

        # 🔥 TRAINING CORE
        epochs=80,
        imgsz=640,
        batch=16,
        patience=20,              # ✅ EARLY STOP aktif

        optimizer="auto",
        pretrained=True,

        # 🔥 AUGMENTATION (penting untuk uang)
        hsv_h=0.015,
        hsv_s=0.7,
        hsv_v=0.4,
        degrees=5,
        translate=0.1,
        scale=0.5,
        fliplr=0.5,

        # 🔥 PERFORMANCE
        device=device,
        workers=4,
        cache=True,
        amp=True,                 # mixed precision (lebih cepat & hemat VRAM)

        # 🔥 LOGGING
        verbose=True,

        name="multi_currency_gpu_v2"
    )

    print("\n✅ Training selesai!")

    # ==============================
    # 5. LOAD BEST MODEL
    # ==============================
    best_model = "runs/detect/multi_currency_gpu_v2/weights/best.pt"

    if not os.path.exists(best_model):
        print("⚠️ Best model tidak ditemukan")
        return

    model = YOLO(best_model)
    print("✅ Best model loaded")

    # ==============================
    # 6. TEST IMAGE
    # ==============================
    test_img = "test.jpg"

    if os.path.exists(test_img):
        print("\n🧪 Testing hasil model...")
        model(test_img, conf=0.4, save=True)
    else:
        print("⚠️ test.jpg tidak ada")

    # ==============================
    # 7. SHOW GRAPH TRAINING
    # ==============================
    result_img = "runs/detect/multi_currency_gpu_v2/results.png"

    if os.path.exists(result_img):
        img = Image.open(result_img)
        plt.imshow(img)
        plt.axis("off")
        plt.title("Training Result")
        plt.show()
    else:
        print("⚠️ Grafik tidak ditemukan")

    print("\n🎉 SELESAI! Model siap dipakai 🚀")


# ==============================
# WINDOWS FIX
# ==============================
if __name__ == "__main__":
    multiprocessing.freeze_support()
    main()