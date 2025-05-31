# .\env\Scripts\Activate
# python app.py

from flask import Flask, request, jsonify
from flask_cors import CORS
from ultralytics import YOLO
import cv2
import numpy as np

app = Flask(__name__)
CORS(app)

model = YOLO('../runs/detect/fandy_bisindo_epoch50_lrauto_batch16_img512/weights/best.pt')

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

    return jsonify(detections)

if __name__ == '__main__':
    app.run(debug=True)
