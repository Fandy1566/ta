from ultralytics import YOLO
import cv2

# Load model dari checkpoint sementara
model = YOLO('../runs/detect/fandy_bisindo_epoch50_lrauto_batch32_img5122/weights/best.pt')  # bisa juga 'best.pt' kalau sudah ada

# Buka kamera (0 untuk kamera default laptop)
cap = cv2.VideoCapture(0)

while True:
    ret, frame = cap.read()
    if not ret:
        break

    # Deteksi dengan YOLO
    results = model.predict(source=frame, conf=0.5, show=False, verbose=False)

    # Ambil hasil prediksi dan gambar dengan bounding box
    annotated_frame = results[0].plot()

    # Tampilkan frame yang sudah di-annotate
    cv2.imshow("YOLOv8 Real-Time", annotated_frame)

    # Tekan 'q' untuk keluar
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
