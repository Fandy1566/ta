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
               class="hidden">
        </video>

        <canvas id="canvas" class="absolute inset-0 z-20"></canvas>

        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-4 bg-white/80 backdrop-blur-md p-2 rounded-full shadow-lg z-20">
            <a href="{{ route('home.index') }}">
                <button class="w-12 h-12 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold">⏻</button>
            </a>
        </div>
    </div>
</div>

<script>
    let lastDetections = [];

    function startCamera() {
        const video = document.getElementById('webcam');
        const errorOverlay = document.getElementById('camera-error');
        const canvas = document.getElementById('canvas');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();

                    const videoRatio = video.videoWidth / video.videoHeight;

                    const maxWidth = video.parentElement.clientWidth;
                    const maxHeight = video.parentElement.clientHeight;

                    let canvasWidth = maxWidth;
                    let canvasHeight = maxWidth / videoRatio;

                    if (canvasHeight > maxHeight) {
                        canvasHeight = maxHeight;
                        canvasWidth = canvasHeight * videoRatio;
                    }

                    canvas.width = canvasWidth;
                    canvas.height = canvasHeight;

                    canvas.style.width = canvasWidth + 'px';
                    canvas.style.height = canvasHeight + 'px';
                    canvas.style.left = ((video.parentElement.clientWidth - canvasWidth) / 2) + 'px';
                    canvas.style.top = ((video.parentElement.clientHeight - canvasHeight) / 2) + 'px';

                    startDetection(video, canvas);
                    errorOverlay.classList.add('hidden');
                };
            })
            .catch((error) => {
                console.error('❌ Kamera gagal:', error);
                errorOverlay.classList.remove('hidden');
            });
    }

    async function startDetection(video, canvas) {
        const context = canvas.getContext('2d');
        const SNAPSHOT_WIDTH = 640;

        const snapshotCanvas = document.createElement('canvas');
        const scale = SNAPSHOT_WIDTH / video.videoWidth;
        const SNAPSHOT_HEIGHT = Math.floor(video.videoHeight * scale);
        snapshotCanvas.width = SNAPSHOT_WIDTH;
        snapshotCanvas.height = SNAPSHOT_HEIGHT;
        const snapCtx = snapshotCanvas.getContext('2d');

        let lastDetections = [];
        let isFetching = false;

        async function fetchDetection() {
            if (isFetching) return;
            isFetching = true;

            snapCtx.drawImage(video, 0, 0, SNAPSHOT_WIDTH, SNAPSHOT_HEIGHT);
            const blob = await new Promise(resolve => snapshotCanvas.toBlob(resolve, 'image/jpeg'));
            const formData = new FormData();
            formData.append('image', blob, 'frame.jpg');

            try {
                const response = await fetch('http://127.0.0.1:5000/predict', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    lastDetections = await response.json();
                }
            } catch (error) {
                console.error("Deteksi gagal:", error);
            } finally {
                isFetching = false;
            }
        }

        setInterval(fetchDetection, 100);

        function renderLoop() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            drawDetections(context, lastDetections, canvas.width, canvas.height, SNAPSHOT_WIDTH, SNAPSHOT_HEIGHT);
            requestAnimationFrame(renderLoop);
        }

        renderLoop();
    }

    function drawDetections(context, detections, canvasWidth, canvasHeight, snapshotWidth, snapshotHeight) {
        context.lineWidth = 2;
        context.strokeStyle = 'lime';
        context.font = '16px Arial';
        context.fillStyle = 'lime';

        const scaleX = canvasWidth / snapshotWidth;
        const scaleY = canvasHeight / snapshotHeight;

        const classLabels = Array.from({length: 27}, (_, i) => String.fromCharCode(65 + i)); 

        detections.forEach(d => {
            const [x1, y1, x2, y2] = d.bbox;
            const classId = d.class_id;
            const label = classLabels[classId] || classId;

            context.beginPath();
            context.rect(x1 * scaleX, y1 * scaleY, (x2 - x1) * scaleX, (y2 - y1) * scaleY);
            context.stroke();

            const confPercent = (d.confidence * 100).toFixed(1);
            context.fillText(`${label} (${confPercent}%)`, x1 * scaleX + 4, y1 * scaleY + 16);
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
