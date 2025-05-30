@extends('layouts.template')

@section('body')
<div class="h-screen flex overflow-hidden">
    <div id="camera" class="flex-1 bg-black relative">
        <div id="camera-error"
             class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-80 text-white text-center text-lg font-semibold z-20 hidden space-y-4 px-4">
            <p>Kamera tidak dapat diakses.<br>Pastikan izin webcam sudah diberikan.</p>
            <button id="retry-camera" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-base">
                Coba Lagi
            </button>
        </div>

        <video id="webcam"
               autoplay
               muted
               playsinline
               class="absolute inset-0 w-full h-full object-cover z-0 bg-red-500">
        </video>

        <canvas id="canvas" class="absolute inset-0 w-full h-full z-10"></canvas>

        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-4 bg-white/80 backdrop-blur-md p-2 rounded-full shadow-lg z-20">
            <button class="w-12 h-12 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold">⏻</button>
        </div>
    </div>
</div>

<script>
    function startCamera() {
        const video = document.getElementById('webcam');
        const errorOverlay = document.getElementById('camera-error');
        const canvas = document.getElementById('canvas');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    startDetection(video, canvas);
                };
                errorOverlay.classList.add('hidden');
            })
            .catch((error) => {
                console.error('❌ Kamera gagal:', error);
                errorOverlay.classList.remove('hidden');
            });
    }

    document.addEventListener("DOMContentLoaded", () => {
        startCamera();

        const retry = document.getElementById('retry-camera');
        retry?.addEventListener('click', () => {
            startCamera();
        });
    });
</script>
@endsection
