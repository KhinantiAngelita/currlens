from flask import Flask, request, jsonify
from flask_cors import CORS
from ultralytics import YOLO
import numpy as np
import cv2
import os

print("✅ All imports successful")

app = Flask(__name__)
print("✅ Flask app created")

CORS(app)
print("✅ CORS enabled")

# =====================================================================
# CHOOSE ACTIVE DETECTOR MODE
# Use "multi" for ASEAN currencies (IDR, SGD, MYR, THB)
# Use "php" for Philippine Currency (PHP and coins)
# =====================================================================
ACTIVE_MODE = "php"  # Change to "php" to use the highly accurate currency_lens_v3_s model!

# ==========================
# LOAD MULTIPLE MODELS
# ==========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
print(f"📁 BASE_DIR: {BASE_DIR}")

MODEL_PATHS = {
    "multi": os.path.join(BASE_DIR, "runs", "detect", "currency_lens_v5_combined-4", "weights", "best.pt"),
    "php": os.path.join(BASE_DIR, "runs", "detect", "currency_lens_v3_s", "weights", "best.pt"),
}

models = {}

for name, path in MODEL_PATHS.items():
    if os.path.exists(path):
        print(f"🔥 LOAD MODEL ({name}): {path}")
        models[name] = YOLO(path)
        print(f"✅ MODEL LOADED ({name}): {path}")
    else:
        print(f"❌ MODEL NOT FOUND ({name}): {path}")

CONF_THRESHOLD = 0.2
IOU_THRESHOLD = 0.5


# ==========================
# HELPER
# ==========================
def parse_class(name):
    try:
        parts = name.split("_")
        if len(parts) != 2:
            return "UNKNOWN", 0
        
        # Format B: <nominal>_<currency_full> (e.g. "1000_Rupiah", "20_Baht", "2_SGD")
        if parts[0].isdigit():
            val = int(parts[0])
            cur_raw = parts[1].upper()
            
            # Map full currency names to standard codes
            if "RUPIAH" in cur_raw:
                cur = "IDR"
            elif "RINGGIT" in cur_raw:
                cur = "MYR"
            elif "BAHT" in cur_raw:
                cur = "THB"
            elif "SGD" in cur_raw:
                cur = "SGD"
            elif "PHP" in cur_raw:
                cur = "PHP"
            else:
                cur = cur_raw
            return cur, val
            
        # Format A: <currency>_<nominal> (e.g. "myr_1", "sgd_2", "idr_1000", "php_20")
        elif parts[1].isdigit():
            val = int(parts[1])
            cur = parts[0].upper()
            return cur, val
            
        else:
            return "UNKNOWN", 0
    except Exception as e:
        print(f"Error parsing class name '{name}': {e}")
        return "UNKNOWN", 0


def format_currency(currency, nominal):
    if currency == "IDR":
        return f"Rp {nominal:,}".replace(",", ".")
    elif currency == "PHP":
        return f"₱ {nominal:,}"
    elif currency == "MYR":
        return f"RM {nominal:,}"
    elif currency == "SGD":
        return f"S$ {nominal:,}"
    elif currency == "THB":
        return f"฿ {nominal:,}"
    else:
        return f"{currency} {nominal:,}"


def iou(box1, box2):
    # box format: [x1,y1,x2,y2]
    x1 = max(box1[0], box2[0])
    y1 = max(box1[1], box2[1])
    x2 = min(box1[2], box2[2])
    y2 = min(box1[3], box2[3])

    inter = max(0, x2 - x1) * max(0, y2 - y1)
    area1 = (box1[2]-box1[0]) * (box1[3]-box1[1])
    area2 = (box2[2]-box2[0]) * (box2[3]-box2[1])

    union = area1 + area2 - inter
    return inter / union if union > 0 else 0


def merge_boxes(all_dets):
    final = []

    for det in sorted(all_dets, key=lambda x: x["confidence"], reverse=True):
        keep = True
        for f in final:
            # Jika kedua deteksi tumpang tindih tinggi (IoU > 0.5), itu adalah lembaran uang yang sama!
            # Pertahankan hanya deteksi dengan confidence score tertinggi (yang diproses duluan).
            if iou(det["box"], f["box"]) > 0.5:
                keep = False
                break
        if keep:
            final.append(det)

    return final


# ==========================
# ROUTE
# ==========================
@app.route("/predict", methods=["POST"])
def predict():
    print("\n📥 REQUEST MASUK")

    if "image" not in request.files:
        return jsonify({"error": "No image"}), 400

    file = request.files["image"]

    img = cv2.imdecode(
        np.frombuffer(file.read(), np.uint8),
        cv2.IMREAD_COLOR
    )

    if img is None:
        return jsonify({"error": "Invalid image"}), 400

    print("📐 IMAGE SHAPE:", img.shape)

    # ==========================
    # RUN ACTIVE MODEL BASED ON ACTIVE_MODE
    # ==========================
    mode = ACTIVE_MODE
    print(f"🎯 MODE DETEKSI (ACTIVE_MODE): {mode}")

    active_models = []
    if mode in models:
        active_models.append(models[mode])
    else:
        # Fallback if no match
        active_models = list(models.values())

    all_detections = []

    for idx, model in enumerate(active_models):
        print(f"\n🚀 RUN MODEL: {active_models[idx]}")

        results = model(img, conf=0.05, iou=IOU_THRESHOLD)

        boxes = results[0].boxes

        if boxes is None:
            continue

        for box in boxes:
            conf = float(box.conf[0])
            if conf < CONF_THRESHOLD:
                continue

            cls = int(box.cls[0])
            label = model.names[cls]

            x1, y1, x2, y2 = box.xyxy[0].tolist()

            all_detections.append({
                "label": label,
                "confidence": conf,
                "box": [x1, y1, x2, y2]
            })

            print(f"→ {label} ({round(conf,2)})")

    print(f"\n🔍 TOTAL RAW DETECTIONS: {len(all_detections)}")

    # ==========================
    # MERGE (REMOVE DUPLICATE)
    # ==========================
    merged = merge_boxes(all_detections)

    print(f"✅ AFTER MERGE: {len(merged)}")

    # ==========================
    # FORMAT OUTPUT
    # ==========================
    detections = []
    totals = {}

    for det in merged:
        currency, nominal = parse_class(det["label"])

        detections.append({
            "label": det["label"],
            "currency": currency,
            "nominal": nominal,
            "readable": format_currency(currency, nominal),
            "confidence": round(det["confidence"], 3)
        })

        if currency != "UNKNOWN":
            totals[currency] = totals.get(currency, 0) + nominal

    total_nominal = sum(totals.values())

    print("📊 FINAL:", len(detections), "objects | TOTAL:", total_nominal)

    return jsonify({
        "total_object": len(detections),
        "total_nominal": total_nominal,
        "totals_by_currency": totals,
        "detections": detections
    })


# ==========================
# RUN
# ==========================
if __name__ == "__main__":
    print("\n" + "="*50)
    print("🚀 STARTING FLASK SERVER")
    print("="*50)
    app.run(debug=True)