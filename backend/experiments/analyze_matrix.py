import os
import torch
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
from ultralytics import YOLO

def analyze_confusion_matrix(run_name='currency_lens_v3_finetune-2'):
    print(f"🔍 Analyzing Confusion Matrix for: {run_name}...")
    
    # 1. SETUP PATHS
    best_weights = os.path.join("backend", "runs", "detect", run_name, "weights", "best.pt")
    data_yaml = "backend/multi_currency_gpu_v3/data.yaml"
    output_dir = os.path.join("backend", "runs", "detect", "benchmark_results")
    os.makedirs(output_dir, exist_ok=True)

    if not os.path.exists(best_weights):
        print(f"❌ Error: Model weights not found at {best_weights}")
        return

    # 2. RUN VALIDATION
    print("⏳ Running validation to extract raw data...")
    model = YOLO(best_weights)
    results = model.val(data=data_yaml, plots=True, save_json=False)
    
    # Extract matrix
    # YOLOv8 confusion matrix: [nc+1, nc+1] where nc is background
    matrix = results.confusion_matrix.matrix
    names = list(model.names.values())
    nc = len(names)
    names_with_bg = names + ["background"]
    
    # 3. NORMALIZE (Row normalization: True vs Predicted)
    row_sums = matrix.sum(axis=1, keepdims=True)
    matrix_norm = np.divide(matrix, row_sums, out=np.zeros_like(matrix), where=row_sums!=0)

    # 4. VISUALIZATION (Diagonal Only)
    print("🎨 Creating diagonal-only confusion matrix image...")
    fig, ax = plt.subplots(figsize=(18, 14))
    im = ax.imshow(matrix_norm, cmap='Blues')
    
    # Add colorbar
    cbar = ax.figure.colorbar(im, ax=ax)
    cbar.ax.set_ylabel("Recall / Confidence Score", rotation=-90, va="bottom")

    # Annotate ONLY diagonal
    for i in range(len(names_with_bg)):
        for j in range(len(names_with_bg)):
            if i == j: # ONLY DIAGONAL
                val = matrix_norm[i, j]
                color = "white" if val > 0.5 else "black"
                # Use bold, larger font for diagonal
                ax.text(j, i, f"{val*100:.0f}%", ha="center", va="center", 
                        color=color, fontsize=10, fontweight='bold')

    # Set labels
    ax.set_xticks(np.arange(len(names_with_bg)))
    ax.set_yticks(np.arange(len(names_with_bg)))
    ax.set_xticklabels(names_with_bg, rotation=45, ha='right', fontsize=9)
    ax.set_yticklabels(names_with_bg, fontsize=9)
    
    plt.title(f"Per-Class Accuracy Analysis: {run_name}", fontsize=16, fontweight='bold')
    plt.xlabel("Predicted Label", fontsize=12)
    plt.ylabel("True Label", fontsize=12)
    
    # Save
    matrix_img_path = os.path.join(output_dir, "confusion_matrix_diagonal_only.png")
    plt.savefig(matrix_img_path, bbox_inches='tight', dpi=300)
    plt.close()
    print(f"✅ Saved Image: {matrix_img_path}")
    
    # 5. ANALYSIS REPORT
    print("📝 Generating text analysis report...")
    report_path = os.path.join(output_dir, "confusion_analysis.txt")
    
    with open(report_path, 'w', encoding='utf-8') as f:
        f.write(f"CONFUSION MATRIX ANALYSIS REPORT: {run_name}\n")
        f.write("="*60 + "\n\n")

        # A. Top 10 Confusions
        f.write("🚨 TOP 10 MOST CONFUSED CLASS PAIRS:\n")
        confusions = []
        for i in range(nc): # Only check true classes (excluding background row)
            for j in range(nc + 1):
                if i == j: continue
                val = matrix_norm[i, j]
                if val > 0.01:
                    confusions.append((names[i], names_with_bg[j], val))
        
        confusions.sort(key=lambda x: x[2], reverse=True)
        for t, p, v in confusions[:10]:
            f.write(f"- {t} MISCLASSIFIED AS {p}: {v*100:.1f}%\n")
            if v > 0.10:
                f.write(f"  💡 RECOMMENDATION: Add more variation or check labels for '{t}'.\n")
        f.write("\n")

        # B. Lowest Recall Classes
        f.write("📉 WEAKEST CLASSES (Lowest Detection Rate / Recall):\n")
        recalls = []
        for i in range(nc):
            recalls.append((names[i], matrix_norm[i, i]))
        
        recalls.sort(key=lambda x: x[1])
        for name, val in recalls[:5]:
            status = "⚠️ CRITICAL" if val < 0.80 else "🟡 IMPROVABLE"
            f.write(f"- {name}: {val*100:.1f}% Accuracy ({status})\n")
            f.write(f"  💡 TIP: Dataset for '{name}' may be insufficient or too uniform.\n")
        f.write("\n")

        # C. Perfect Classes
        perfect = [names[i] for i in range(nc) if matrix_norm[i, i] > 0.98]
        if perfect:
            f.write(f"✅ NEAR-PERFECT CLASSES (>98% Accuracy):\n")
            f.write(", ".join(perfect) + "\n")

    print(f"✅ Saved Report: {report_path}")

if __name__ == "__main__":
    analyze_confusion_matrix()
