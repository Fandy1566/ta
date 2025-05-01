# pip install ultralytics

from ultralytics import YOLO

epochs_list = [50, 100, 150, 200]
lr_list = [0.01, 0.001, 0.0001]
batch_list = [16, 32]
imgsz_list = [512, 640]

for epoch in epochs_list:
    for lr0 in lr_list:
        for batch in batch_list:
            for imgsz in imgsz_list:
                model = YOLO("yolov8n.pt")

                model_name = f"SIBI_epoch{epoch}_lr{lr0}_batch{batch}_img{imgsz}"

                model.train(
                    data="dataset/data.yaml",
                    epochs=epoch,
                    imgsz=imgsz,
                    batch=batch,
                    lr0=lr0,
                    name=model_name
                )