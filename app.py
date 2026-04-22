from flask import Flask, request, jsonify
from ultralytics import YOLO
import numpy as np
import cv2

app = Flask(__name__)

# ==============================
# LOAD MODEL
# ==============================
model = YOLO("runs/detect/currency_model-3/weights/best.pt")
names = model.names  # nama class

# ==============================
# ROUTE HOME
# ==============================
@app.route("/")
def home():
    return "🚀 Flask YOLO API is running!"

# ==============================
# ROUTE TEST
# ==============================
@app.route("/test", methods=["GET"])
def test():
    return {"status": "API OK"}

# ==============================
# ROUTE PREDICT
# ==============================
@app.route("/predict", methods=["POST"])
def predict():
    # cek apakah ada file
    if "image" not in request.files:
        return jsonify({"error": "No image uploaded"}), 400

    file = request.files["image"]

    if file.filename == "":
        return jsonify({"error": "Empty file"}), 400

    # convert file ke image OpenCV
    img = cv2.imdecode(
        np.frombuffer(file.read(), np.uint8),
        cv2.IMREAD_COLOR
    )

    if img is None:
        return jsonify({"error": "Invalid image"}), 400

    # ==============================
    # PREDIKSI
    # ==============================
    results = model(img, conf=0.25)
    boxes = results[0].boxes

    output = []

    if boxes is not None and len(boxes) > 0:
        for box in boxes:
            x1, y1, x2, y2 = box.xyxy[0].tolist()
            conf = float(box.conf[0])
            cls = int(box.cls[0])

            output.append({
                "class_id": cls,
                "class_name": names[cls],
                "confidence": round(conf, 3),
                "box": [round(x1, 2), round(y1, 2), round(x2, 2), round(y2, 2)]
            })

    else:
        return jsonify({
            "message": "No object detected",
            "result": []
        })

    return jsonify({
        "total_object": len(output),
        "result": output
    })


# ==============================
# RUN APP
# ==============================
if __name__ == "__main__":
    app.run(port=5000, debug=True)