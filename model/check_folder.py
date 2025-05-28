import os

epochs_list = [50]
lr_list = [0.01]
batch_list = [16]
imgsz_list = [512]

results = []

for epoch in epochs_list:
    for lr0 in lr_list:
        for batch in batch_list:
            for imgsz in imgsz_list:
                model_name = f"BISINDO_epoch{epoch}_lr{lr0}_batch{batch}_img{imgsz}"
                model_dir = f"../runs/detect/{model_name}"
                checkpoint_path = os.path.join(model_dir, "weights", "last.pt")
                print(checkpoint_path)

                if os.path.exists(checkpoint_path):
                    print(f"📌 Melanjutkan training dari checkpoint: {checkpoint_path}")
                    # model = YOLO(checkpoint_path)
                    resume_flag = True
                else:
                    print(f"🆕 Mulai training baru: {model_name}")
                    # model = YOLO("yolov8n.pt")
                    resume_flag = False