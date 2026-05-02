import os
import torch
import multiprocessing
import pandas as pd
import matplotlib.pyplot as plt
from ultralytics import YOLO
import sys

# ==========================================
# CONFIGURATION & FOLDER MANAGEMENT
# ==========================================
BASE_RUNS = os.path.join("runs", "detect")
NANO_RUN = "currency_lens_v3_nano"
SMALL_RUN = "currency_lens_v3_s"
BENCHMARK_DIR = os.path.join(BASE_RUNS, "benchmark_results")

os.makedirs(BENCHMARK_DIR, exist_ok=True)

def compare_models(nano_run_name=NANO_RUN, small_run_name=SMALL_RUN):
    """Goal 3, 4, 6: Benchmark YOLOv8n vs YOLOv8s with robust visualization"""
    print(f"\n📊 Comparing {nano_run_name} vs {small_run_name}...")
    
    nano_csv = os.path.join(BASE_RUNS, nano_run_name, "results.csv")
    small_csv = os.path.join(BASE_RUNS, small_run_name, "results.csv")

    # Goal 6: Error Handling
    if not os.path.exists(nano_csv) or not os.path.exists(small_csv):
        print(f"⚠️ Warning: Missing CSV files for comparison.")
        print(f"   Nano CSV: {'Found' if os.path.exists(nano_csv) else 'Missing'}")
        print(f"   Small CSV: {'Found' if os.path.exists(small_csv) else 'Missing'}")
        return

    try:
        # Read and clean Data
        df_n = pd.read_csv(nano_csv); df_n.columns = [c.strip() for c in df_n.columns]
        df_s = pd.read_csv(small_csv); df_s.columns = [c.strip() for c in df_s.columns]

        metrics = {
            'metrics/mAP50(B)': 'map50_comparison.png',
            'metrics/mAP50-95(B)': 'map5095_comparison.png'
        }

        for metric, filename in metrics.items():
            if metric not in df_n.columns or metric not in df_s.columns:
                print(f"⚠️ Column {metric} not found in one of the CSVs. Skipping.")
                continue
                
            plt.figure(figsize=(10, 6))
            plt.plot(df_n['epoch'], df_n[metric], label='YOLOv8n (Nano)', color='#2ecc71', linewidth=2, marker='o', markersize=3, alpha=0.8)
            plt.plot(df_s['epoch'], df_s[metric], label='YOLOv8s (Small)', color='#3498db', linewidth=2, marker='s', markersize=3, alpha=0.8)
            
            # Goal 4: Formatting
            plt.title(f"Performance Comparison: {metric.split('/')[-1]}", fontweight='bold', fontsize=14)
            plt.xlabel("Epoch", fontsize=12)
            plt.ylabel("Value", fontsize=12)
            plt.legend(loc='lower right')
            plt.grid(True, linestyle='--', alpha=0.4)
            plt.tight_layout() # Goal 3
            
            save_path = os.path.join(BENCHMARK_DIR, filename)
            plt.savefig(save_path, dpi=300)
            print(f"✅ Saved comparison: {save_path}")
            plt.close() # Goal 3: Prevent memory leak

    except Exception as e:
        print(f"❌ Error during plotting: {e}")

def train_model(model_type='s'):
    """Goal 1, 2, 5: Train YOLO model optimized for laptop hardware"""
    
    # Goal 1: Simple & Reliable GPU Detection
    device = "0" if torch.cuda.is_available() else "cpu"
    device_name = torch.cuda.get_device_name(0) if torch.cuda.is_available() else "CPU"
    print(f"\n💻 Active Device: {device_name} ({'CUDA' if torch.cuda.is_available() else 'Fallback to CPU'})")
    
    model_file = os.path.join("models", "yolov8s.pt" if model_type == 's' else "yolov8n.pt")
    run_name = SMALL_RUN if model_type == 's' else NANO_RUN
    
    print(f"🚀 Training {model_file} as {run_name}...")
    
    model = YOLO(model_file)
    
    # Goal 2: Optimization for RTX 3050 Ti (4GB VRAM)
    try:
        model.train(
            data=os.path.join("data", "multi_currency_gpu_v3", "data.yaml"),
            epochs=80,
            imgsz=640,
            batch=12,         # Goal 2: Safer for 4GB VRAM
            device=device,
            name=run_name,
            exist_ok=False,
            pretrained=True,
            workers=4,        # Goal 2: Prevent CPU overload
            amp=True          # Goal 2: Faster training
        )
        
        # Goal 5: Optional Inference Test
        best_path = os.path.join(BASE_RUNS, run_name, "weights", "best.pt")
        test_img_path = os.path.join("test", "test.jpg")
        if os.path.exists(best_path) and os.path.exists(test_img_path):
            print(f"\n🧪 Running quick validation on {test_img_path}...")
            test_model = YOLO(best_path)
            test_model(test_img_path, conf=0.3, iou=0.5, save=True)
            print(f"✅ Validation image saved to: {BASE_RUNS}/{run_name}/predict/")

        print(f"\n🎉 Training Complete! Model saved in: {BASE_RUNS}/{run_name}")

    except Exception as e:
        print(f"❌ Training failed: {e}")

if __name__ == "__main__":
    multiprocessing.freeze_support()
    
    # Goal 7: CLI Usage
    if len(sys.argv) > 1:
        cmd = sys.argv[1]
        if cmd == '--train-s':
            train_model('s')
        elif cmd == '--train-n':
            train_model('n')
        elif cmd == '--compare':
            compare_models()
        else:
            print(f"Unknown command: {cmd}")
    else:
        print("\n💰 Currency Lens Training CLI")
        print("Usage:")
        print("  python training/train.py --train-s   (Train YOLOv8s - Recommended)")
        print("  python training/train.py --train-n   (Train YOLOv8n - Fast)")
        print("  python training/train.py --compare   (Compare Nano vs Small results)")