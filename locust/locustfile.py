from locust import HttpUser, task, between
import os
import mimetypes

class APIUser(HttpUser):
    wait_time = between(1, 1)

    def on_start(self):
        image_path = "../model/dataset/tugas-akhir-fandy.v8i.yolov8/test/images/A_38_png.rf.0ac27c57e7f7a9a00054933e295bdc02.jpg"

        if not os.path.isfile(image_path):
            raise FileNotFoundError(f"File gambar tidak ditemukan: {image_path}")

        with open(image_path, "rb") as image_file:
            self.image_bytes = image_file.read()

        self.mime_type = mimetypes.guess_type(image_path)[0] or 'application/octet-stream'
        self.file_name = os.path.basename(image_path)

    @task
    def send_image(self):
        files = {
            'image': (self.file_name, self.image_bytes, self.mime_type)
        }

        with self.client.post("/predict", files=files, catch_response=True) as response:
            if response.status_code != 200:
                response.failure(f"Unexpected status code: {response.status_code}")
            else:
                pass
