# ==============================
# YOLOv8 TRAINING - MULTI CURRENCY
# ==============================

from ultralytics import YOLO
import os
from PIL import Image
import matplotlib.pyplot as plt
import torch
import multiprocessing


def main():

    # ==============================
    # 0. CEK GPU
    # ==============================
    print("\n🔍 Checking device...")
    if torch.cuda.is_available():
        device = 0
        print(f"🔥 GPU terdeteksi: {torch.cuda.get_device_name(0)}")
    else:
        device = "cpu"
        print("⚠️ GPU tidak terdeteksi, menggunakan CPU")

    # ==============================
    # 1. CEK FILE YAML
    # ==============================
    data_yaml = "multi_currency.yml"

    if not os.path.exists(data_yaml):
        print("❌ ERROR: multi_currency.yml tidak ditemukan!")
        return
    else:
        print("✅ YAML ditemukan:", data_yaml)

    # ==============================
    # 2. LOAD MODEL
    # ==============================
    print("\n🚀 Loading model...")
    model = YOLO("yolov8n.pt")  # bisa ganti yolov8s.pt untuk lebih akurat

    # ==============================
    # 3. TRAINING
    # ==============================
    print("\n🚀 Training dimulai...\n")

    results = model.train(
        data=data_yaml,
        epochs=50,
        imgsz=640,
        batch=16,
        patience=10,
        name="multi_currency_model",

        optimizer="auto",
        pretrained=True,
        verbose=True,

        device=device,
        workers=0  # penting untuk Windows
    )

    print("\n✅ Training selesai!")

    # ==============================
    # 4. LOAD MODEL TERBAIK
    # ==============================
    best_model_path = "runs/detect/multi_currency_model/weights/best.pt"

    if os.path.exists(best_model_path):
        print("\n📦 Loading best model...")
        model = YOLO(best_model_path)
        print("✅ Model terbaik berhasil dimuat")
    else:
        print("⚠️ Model terbaik tidak ditemukan")

    # ==============================
    # 5. TEST PREDICTION
    # ==============================
    test_image = "test.jpg"

    if os.path.exists(test_image):
        print("\n🧪 Testing model...")
        results = model(test_image, save=True)
        print("✅ Hasil prediksi disimpan di folder runs/")
    else:
        print("⚠️ test.jpg tidak ditemukan, skip testing")

    # ==============================
    # 6. TAMPILKAN GRAFIK
    # ==============================
    results_img_path = "runs/detect/multi_currency_model/results.png"

    if os.path.exists(results_img_path):
        print("\n📊 Menampilkan grafik training...")
        img = Image.open(results_img_path)
        plt.imshow(img)
        plt.axis('off')
        plt.title("Training Results (Loss, mAP, Precision, Recall)")
        plt.show()
    else:
        print("⚠️ Grafik tidak ditemukan")

    print("\n🎉 SELESAI! Model siap digunakan 🚀")


# ==============================
# WAJIB UNTUK WINDOWS
# ==============================
if __name__ == "__main__":
    multiprocessing.freeze_support()
    main()