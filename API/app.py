# Cara menjalankan:
# .\env\Scripts\Activate
# python app.py

from flask import Flask, request, jsonify
from flask_cors import CORS
from ultralytics import YOLO
import cv2
import numpy as np
import psutil
import GPUtil

app = Flask(__name__)
CORS(app)

# Load YOLO model
model = YOLO('../runs/detect/v4_manual_fandy_bisindo_epoch20_lrauto_batch16_img640/weights/best.pt')

def print_resource_usage():
    print("\n== Resource Usage ==")
    print(f"CPU Usage: {psutil.cpu_percent()}%")
    print(f"Memory Usage: {psutil.virtual_memory().percent}%")

    gpus = GPUtil.getGPUs()
    for gpu in gpus:
        print(f"GPU {gpu.id}: {gpu.name}")
        print(f"  Load: {gpu.load * 100:.1f}%")
        print(f"  Memory: {gpu.memoryUsed}MB / {gpu.memoryTotal}MB")

@app.route('/predict', methods=['POST'])
def predict():
    if 'image' not in request.files:
        return jsonify({'error': 'No image provided'}), 400

    file = request.files['image']
    image_bytes = file.read()
    nparr = np.frombuffer(image_bytes, np.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

    results = model(img)

    detections = []
    for r in results:
        for box in r.boxes:
            detections.append({
                'class_id': int(box.cls),
                'confidence': float(box.conf),
                'bbox': list(map(int, box.xyxy[0]))
            })

    print_resource_usage()
    return jsonify(detections)

if __name__ == '__main__':
    app.run(debug=True)
