from ultralytics import YOLO
import torch
import multiprocessing

def main():
    if torch.cuda.is_available():
        epochs_list = [20]
        batch_list = [32]
        imgsz_list = [640]

        for epoch in epochs_list:
            for batch in batch_list:
                for imgsz in imgsz_list:
                    model_name = f"v4_manual_fandy_bisindo_epoch{epoch}_lrauto_batch{batch}_img{imgsz}"
                    model = YOLO("yolov8n.pt")

                    device = "cuda" if torch.cuda.is_available() else "cpu"
                    
                    model.train(
                        data="dataset/tugas-akhir-fandy.v8i.yolov8/data.yaml",
                        epochs=epoch,
                        imgsz=imgsz,
                        batch=batch,
                        name=model_name,
                        device=device,
                        fliplr=0.5,
                        flipud=0.0,
                        hsv_h=0.015,
                        hsv_s=0.7,
                        hsv_v=0.4,
                        mosaic=0.5,
                        scale=0.5,
                        degrees=5,
                    )

        print("Training selesai.")

if __name__ == '__main__':
    multiprocessing.freeze_support()
    main()

                        # translate=0.1,
                        # shear=5,
                        # perspective=0.001,