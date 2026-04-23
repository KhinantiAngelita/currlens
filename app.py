from flask import Flask, request, jsonify
from ultralytics import YOLO
import numpy as np
import cv2
import os

app = Flask(__name__)

# ==============================
# ROOT PROJECT
# ==============================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# ==============================
# LOAD MODEL (ABSOLUTE PATH)
# ==============================
MODEL_PATH = os.path.join(
    BASE_DIR,
    "runs",
    "detect",
    "multi_currency_gpu_v2",
    "weights",
    "best.pt"
)

print("📦 Loading model from:", MODEL_PATH)

if not os.path.exists(MODEL_PATH):
    raise FileNotFoundError(f"❌ Model tidak ditemukan di: {MODEL_PATH}")

model = YOLO(MODEL_PATH)
names = model.names


# ==============================
# FORMAT CLASS NAME → NOMINAL
# ==============================
def format_currency(class_name):
    try:
        parts = class_name.split("_")

        if len(parts) >= 2:
            nominal = int(parts[0])
            currency = parts[1]
            country = parts[2] if len(parts) >= 3 else None

            readable = f"{nominal} {currency}"
            if country:
                readable += f" ({country})"

            return {
                "nominal": nominal,
                "currency": currency,
                "country": country,
                "readable": readable
            }

    except:
        pass

    return {
        "nominal": None,
        "currency": None,
        "country": None,
        "readable": class_name
    }


# ==============================
# ROUTE HOME
# ==============================
@app.route("/")
def home():
    return "🚀 YOLO Currency API Running"


# ==============================
# ROUTE TEST
# ==============================
@app.route("/test", methods=["GET"])
def test():
    return {"status": "OK"}


# ==============================
# ROUTE PREDICT
# ==============================
@app.route("/predict", methods=["POST"])
def predict():

    # ==============================
    # VALIDASI INPUT
    # ==============================
    if "image" not in request.files:
        return jsonify({"error": "No image uploaded"}), 400

    file = request.files["image"]

    if file.filename == "":
        return jsonify({"error": "Empty file"}), 400

    # ==============================
    # CONVERT IMAGE
    # ==============================
    img = cv2.imdecode(
        np.frombuffer(file.read(), np.uint8),
        cv2.IMREAD_COLOR
    )

    if img is None:
        return jsonify({"error": "Invalid image"}), 400

    # ==============================
    # PREDICT
    # ==============================
    results = model(img, conf=0.25)
    boxes = results[0].boxes

    output = []

    # ==============================
    # PROCESS RESULT
    # ==============================
    if boxes is not None and len(boxes) > 0:
        for box in boxes:

            conf = float(box.conf[0])

            # 🔥 FILTER confidence
            if conf < 0.5:
                continue

            x1, y1, x2, y2 = box.xyxy[0].tolist()
            cls = int(box.cls[0])

            class_name = names[cls]
            parsed = format_currency(class_name)

            output.append({
                "class_id": cls,
                "class_name": class_name,

                "nominal": parsed["nominal"],
                "currency": parsed["currency"],
                "country": parsed["country"],
                "readable": parsed["readable"],

                "confidence": round(conf, 3),
                "box": [
                    round(x1, 2),
                    round(y1, 2),
                    round(x2, 2),
                    round(y2, 2)
                ]
            })

    # ==============================
    # NO DETECTION
    # ==============================
    if len(output) == 0:
        return jsonify({
            "message": "No confident detection",
            "result": []
        })

    # ==============================
    # TOTAL NOMINAL
    # ==============================
    total_nominal = sum([
        obj["nominal"] for obj in output if obj["nominal"]
    ])

    # ==============================
    # TOTAL PER CURRENCY
    # ==============================
    totals_by_currency = {}

    for obj in output:
        if obj["currency"] and obj["nominal"]:
            key = obj["currency"]
            totals_by_currency[key] = totals_by_currency.get(key, 0) + obj["nominal"]

    # ==============================
    # RESPONSE
    # ==============================
    return jsonify({
        "total_object": len(output),
        "total_nominal": total_nominal,
        "totals_by_currency": totals_by_currency,
        "result": output
    })


# ==============================
# RUN APP
# ==============================
if __name__ == "__main__":
    app.run(port=5000, debug=True)