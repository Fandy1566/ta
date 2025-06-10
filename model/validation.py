from ultralytics import YOLO
import torch
from itertools import product

def validate_model(model_name, imgsz, batch, device):
    best_model_path = f"../runs/detect/{model_name}/weights/best.pt"
    val_model = YOLO(best_model_path)
    
    metrics = val_model.val(
        data="dataset/tugas-akhir-fandy.v8i.yolov8/data.yaml",
        imgsz=imgsz,
        batch=batch,
        conf=0.7,
        iou=0.5,
        device=device
    )
    
    print(f"✅ Validasi selesai untuk model: {model_name}")
    # Jika ingin menyimpan metrics: return metrics

if __name__ == "__main__":
    device = "cuda" if torch.cuda.is_available() else "cpu"
    
    epochs = [20]
    img_sizes = [640]
    batches = [16]

    print("="*120)
    print("conf=0.1,iou=0.5")

    # Loop semua kombinasi batch dan image size
    for epoch, batch, imgsz in product(epochs, batches, img_sizes):
        model_name = f"v4_manual_fandy_bisindo_epoch{epoch}_lrauto_batch{batch}_img{imgsz}"
        try:
            validate_model(model_name, imgsz, batch, device)
        except Exception as e:
            print(f"❌ Gagal validasi model {model_name}: {e}")
