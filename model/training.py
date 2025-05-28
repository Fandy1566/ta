import os
import pandas as pd
from ultralytics import YOLO
import torch

def main():
    if torch.cuda.is_available():
        # Parameters
        # epochs_list = [50, 100, 150, 200]
        # lr_list = [0.01, 0.001, 0.0001] # auto 0.000333, momentum=0.9dengan adam optimizer jadi dari optimizer
        # batch_list = [8, 16, 32]
        # imgsz_list = [512, 640]

        epochs_list = [50]
        # lr_list = [0.01]
        batch_list = [32]
        imgsz_list = [512, 640]

        for epoch in epochs_list:
            # for lr0 in lr_list:
            for batch in batch_list:
                for imgsz in imgsz_list:
                    model_name = f"fandy_bisindo_epoch{epoch}_lrauto_batch{batch}_img{imgsz}"
                    model_dir = f"../runs/detect/{model_name}"
                    checkpoint_path = os.path.join(model_dir, "weights", "last.pt")

                    if os.path.exists(checkpoint_path):
                        print(f"Terdapat file sebelumnya, jadi...")
                        print(f"Training dari checkpoint: {checkpoint_path}")
                        model = YOLO(checkpoint_path)
                        resume_flag = True
                    else:
                        print(f"Tidak terdapat file sebelumnya, jadi...")
                        print(f"Mulai training baru: {model_name}")
                        model = YOLO("yolov8n.pt")
                        resume_flag = False

                    # start_time = time.time()

                    # Set device dynamically
                    device = "cuda" if torch.cuda.is_available() else "cpu"
                    
                    model.train(
                        data="dataset/bisindoyolov8/data.yaml",
                        epochs=epoch,
                        imgsz=imgsz,
                        batch=batch,
                        # lr0=lr0,
                        name=model_name,
                        device=device,
                        resume=resume_flag
                    )

                    # end_time = time.time()
                    # duration = round(end_time - start_time, 2)

        print("\n✅ Semua training selesai.")

if __name__ == '__main__':
    import multiprocessing
    multiprocessing.freeze_support()
    main()
