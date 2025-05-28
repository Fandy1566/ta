import os
import random
import shutil

# Folder asal (data mentah)
source_dir = "dataset/BISINDO.v6i.yolov8"

# Folder tujuan (struktur YOLO hasil copy)
target_dir = "dataset/bisindo2"

# Buat struktur folder di bisindo3
for split in ['train', 'valid', 'test']:
    os.makedirs(os.path.join(target_dir, split, 'images'), exist_ok=True)
    os.makedirs(os.path.join(target_dir, split, 'labels'), exist_ok=True)

# Ambil semua gambar (jpg/png) dari bisindo2
all_images = [f for f in os.listdir(source_dir) if f.endswith('.jpg') or f.endswith('.png')]
all_annotations = [f.replace('.jpg', '.txt').replace('.png', '.txt') for f in all_images]

# Gabungkan pasangan image-label
data = list(zip(all_images, all_annotations))

# Acak data
random.shuffle(data)

# Tentukan proporsi
train_pct = 0.8
valid_pct = 0.1
test_pct = 0.1

# Hitung jumlah data
total = len(data)
train_size = int(total * train_pct)
valid_size = int(total * valid_pct)

# Bagi data
train_data = data[:train_size]
valid_data = data[train_size:train_size + valid_size]
test_data = data[train_size + valid_size:]

# Fungsi salin data ke folder baru
def copy_data(data_list, split):
    for image, annotation in data_list:
        src_img = os.path.join(source_dir, image)
        src_lbl = os.path.join(source_dir, annotation)

        dst_img = os.path.join(target_dir, split, 'images', image)
        dst_lbl = os.path.join(target_dir, split, 'labels', annotation)

        if os.path.exists(src_img) and os.path.exists(src_lbl):
            shutil.copy(src_img, dst_img)
            shutil.copy(src_lbl, dst_lbl)

# Jalankan penyalinan
copy_data(train_data, 'train')
copy_data(valid_data, 'valid')
copy_data(test_data, 'test')

print("✅ Dataset berhasil disalin ke folder bisindo3 dengan struktur YOLO.")
