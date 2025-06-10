from ultralytics import YOLO
import cv2

model = YOLO('../runs/detect/v4_manual_fandy_bisindo_epoch20_lrauto_batch16_img640/weights/best.pt')

cap = cv2.VideoCapture(0)

while True:
    ret, frame = cap.read()
    if not ret:
        break

    results = model.predict(source=frame, conf=0.7, iou=0.5, show=False, verbose=False)

    annotated_frame = results[0].plot()

    cv2.imshow("YOLOv8 Real-Time", annotated_frame)
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
