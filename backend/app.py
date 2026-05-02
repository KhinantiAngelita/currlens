from flask import Flask, request, jsonify
from flask_cors import CORS
from ultralytics import YOLO
import numpy as np
import cv2
import os

app = Flask(__name__)
CORS(app)

# ==========================
# LOAD MULTIPLE MODELS
# ==========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

MODEL_PATHS = [
    os.path.join(BASE_DIR, "runs", "detect", "currency_lens_v3_s", "weights", "best.pt"),
    # os.path.join(BASE_DIR, "runs", "detect", "multi_currency_gpu_v2", "weights", "best.pt"),
    # os.path.join(BASE_DIR, "runs", "detect", "currency_lens_v3_finetune-2", "weights", "best.pt"),
]

models = []

for path in MODEL_PATHS:
    if os.path.exists(path):
        print(f"🔥 LOAD MODEL: {path}")
        models.append(YOLO(path))
    else:
        print(f"❌ MODEL NOT FOUND: {path}")

CONF_THRESHOLD = 0.2
IOU_THRESHOLD = 0.5


# ==========================
# HELPER
# ==========================
def parse_class(name):
    try:
        cur, val = name.split("_")
        return cur.upper(), int(val)
    except:
        return "UNKNOWN", 0


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
            if det["label"] == f["label"]:
                if iou(det["box"], f["box"]) > 0.6:
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
    # RUN ALL MODELS
    # ==========================
    all_detections = []

    for idx, model in enumerate(models):
        print(f"\n🚀 RUN MODEL {idx+1}")

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
            "readable": f"{currency} {nominal:,}",
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
    app.run(debug=True)