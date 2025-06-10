@extends('layouts.template')

@section('body')
<div class="h-screen flex overflow-hidden">

    {{-- Kamera Section --}}
    <div id="camera" class="flex-1 bg-black relative">
        <div id="camera-error" class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-80 text-white text-center text-lg font-semibold z-20 hidden space-y-4 px-4">
            <p>Kamera tidak dapat diakses.<br>Pastikan izin webcam sudah diberikan.</p>
            <button id="retry-camera" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-base">
                Coba Lagi
            </button>
        </div>
        <video id="webcam" autoplay muted playsinline class="hidden inset-0 w-full h-full object-cover z-0"></video>
        <canvas id="canvas" class="absolute inset-0 w-full h-full z-10"></canvas>

        {{-- Tombol kontrol bawah --}}
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-4 bg-white/80 backdrop-blur-md p-2 rounded-full shadow-lg z-20">
            <button type="button" id="helpBtn" class="w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold">✋</button>
            <button type="button" id="hintBtn" class="w-12 h-12 rounded-full bg-green-600 hover:bg-green-700 text-white font-bold">💡</button>
            <button type="button" id="exitBtn" class="w-12 h-12 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold">⏻</button>
            {{-- <button id="debug-correct" class="w-12 h-12 rounded-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold">🐞</button> --}}
        </div>

        {{-- Timer --}}
        <div id="timer" class="absolute top-4 left-1/2 transform -translate-x-1/2 bg-white px-4 py-2 rounded-md shadow-md text-center z-20">
            <span class="text-lg font-bold text-gray-800" id="timer-span">00</span>
        </div>

        {{-- Peragakan kata --}}
        <div class="absolute top-4 right-4 bg-white p-4 rounded-md shadow-lg z-20 w-fit">
            <p class="text-sm font-semibold text-gray-700 mb-2">Peragakan:</p>
            <div id="word-display" class="flex gap-1 flex-wrap"></div>
        </div>
    </div>

    {{-- Detail Sidebar --}}
    <div id="detail" class="bg-white w-[300px] p-5 flex flex-col gap-6 shadow-lg overflow-y-auto border-l border-gray-200">
        {{-- User Info --}}
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-slate-300 rounded-full flex items-center justify-center text-lg font-bold text-gray-600">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                <p class="text-sm {{ Auth::user()->is_premium() ? 'text-yellow-500' : 'text-gray-400' }}">
                    {{ Auth::user()->is_premium() ? 'Premium' : 'Bronze' }}
                </p>
            </div>
        </div>

        <hr class="border-gray-300">

        <div>
            <div class="flex justify-between mb-3">
                <button id="btn-ranking" class="flex-grow bg-blue-600 text-white text-sm font-medium py-1 rounded-l-md">Ranking</button>
                <button id="btn-players" class="flex-grow bg-white text-blue-600 border border-l-0 border-blue-600 text-sm font-medium py-1 rounded-r-md">Pemain</button>
            </div>

            {{-- Ranking List --}}
            <div id="ranking-tab" class="space-y-2">
                @foreach ($players->sortByDesc('score') as $player)
                    <div class="flex justify-between items-center bg-gray-100 border rounded px-3 py-2">
                        <span class="font-medium text-gray-700 truncate">{{ $player->user->name }}</span>
                        <span class="text-sm text-gray-600">{{ $player->score }} pts</span>
                    </div>
                @endforeach
            </div>

            {{-- Player List --}}
            <div id="players-tab" class="space-y-2 hidden">
                <input type="text" id="player-search" placeholder="Cari pemain..." class="w-full px-3 py-2 border rounded-md text-sm">
                
                <div id="players-list" class="space-y-2">
                    @foreach ($players->sortBy(fn($p) => $p->user->name)->values() as $player)
                        <div class="player-item flex justify-between items-center bg-gray-50 border rounded px-3 py-2">
                            <span class="font-medium text-gray-700 truncate">{{ $player->user->name }}</span>
                            <span class="text-sm text-gray-500">{{ $player->user->email }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="helpModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 class="text-lg font-semibold mb-4">Konfirmasi Bantuan</h2>
            <p class="mb-6">Apakah kamu yakin ingin meminta bantuan?</p>
            <div class="flex justify-end space-x-2">
            <button id="cancelHelpBtn" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded">Batal</button>
            <button id="confirmHelpBtn" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded">Ya, Minta Bantuan</button>
            </div>
        </div>
    </div>

    <div id="hintModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-4 max-w-lg">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold">Petunjuk Bahasa Isyarat</h2>
                <button id="closeHint" class="text-gray-600 hover:text-gray-800 text-xl">&times;</button>
            </div>
            <img src="{{ asset('BISINDO/bisindo.jpg') }}" alt="Gambar Bahasa Isyarat BISINDO" class="w-full h-auto rounded" />
        </div>
    </div>



</div>

<script>
    const questions = @json($questions);
    let currentQuestionIndex = {{$player->correct + $player->wrong}};
    let currentCharIndex = 0;
    let timeLeft = questions[currentQuestionIndex]?.time_limit;
    const timerElement = document.getElementById('timer-span');
    const wordDisplay = document.getElementById('word-display');

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

    function startTimer() {
        if (timeLeft === null || timeLeft === undefined) {
            timerElement.textContent = "Tidak ada waktu limit";
            return;
        }

        timerElement.textContent = timeLeft + ' detik';
        
        const interval = setInterval(() => {
            if (timeLeft > 0) {
                timeLeft--;
                timerElement.textContent = timeLeft + ' detik';
            } else {
                goToNextQuestion();
                clearInterval(interval);
            }
        }, 1000);
    }
    async function markAnswer(isCorrect) {
        try {
            const response = await fetch('/game/updateScore', {
                method: 'POST',
                body: JSON.stringify({
                    correct: isCorrect,
                    user_id: {{ Auth::user()->id }},
                    game_room_id: {{$room->id}},
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                markAnswer(isCorrect)
                throw new Error('Failed to update answer status, retrying');
            }

            const data = await response.json();
            fetchAndRenderPlayers();
            console.log(`Answer marked as ${isCorrect ? 'correct' : 'wrong'}`, data);
        } catch (error) {
            markAnswer(isCorrect)
            console.error('Error marking answer:', error);
            alert('Terjadi kesalahan saat memperbarui status jawaban. Silakan coba lagi.');
        }
    }

    function displayWord() {
        if (currentQuestionIndex >= questions.length) {
            document.getElementById('word-display').innerHTML = "<p>Harap tunggu hingga semua pemain berhasil menjawab soal.</p>";
            return;
        }

        wordDisplay.innerHTML = '';
        const chars = questions[currentQuestionIndex].question_text.toUpperCase().split('');
        chars.forEach((char) => {
            const charDiv = document.createElement('div');
            charDiv.classList.add('w-10', 'h-10', 'bg-gray-300', 'border', 'rounded-md', 'grid', 'place-items-center', 'text-lg', 'font-semibold');
            charDiv.textContent = char;
            wordDisplay.appendChild(charDiv);
        });
        currentCharIndex = 0;
    }

    function simulateCorrectGesture() {
        const charElements = wordDisplay.children;
        if (currentCharIndex < charElements.length) {
            charElements[currentCharIndex].classList.remove('bg-gray-300');
            charElements[currentCharIndex].classList.add('bg-green-400', 'text-white');
            currentCharIndex++;

            if (currentCharIndex === charElements.length) {
                // alert("Kata selesai! (Debug)");
                goToNextQuestion();
            }
        }
    }

    function goToNextQuestion() {
        if (currentCharIndex === 0) {
            markAnswer(false);
        } else {
            markAnswer(true);
        }
        markAnswer(true);
        currentQuestionIndex++;

        timeLeft = questions[currentQuestionIndex].time_limit;
        startTimer();
        displayWord();
    }

    function main() {
        startCamera();
        startTimer();
        displayWord();
    }

    document.addEventListener('DOMContentLoaded', main);

    document.querySelector('.bg-green-600').addEventListener('click', () => {
        fetch('/help-request', {
            method: 'POST',
            body: JSON.stringify({ user_id: {{ Auth::user()->id }} }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => console.log("Help requested", data));
    });

    document.addEventListener('DOMContentLoaded', () => {
        const btnRanking = document.getElementById('btn-ranking');
        const btnPlayers = document.getElementById('btn-players');
        const tabRanking = document.getElementById('ranking-tab');
        const tabPlayers = document.getElementById('players-tab');
        const searchInput = document.getElementById('player-search');

        btnRanking.addEventListener('click', () => {
            btnRanking.classList.add('bg-blue-600', 'text-white');
            btnRanking.classList.remove('bg-white', 'text-blue-600');

            btnPlayers.classList.remove('bg-blue-600', 'text-white');
            btnPlayers.classList.add('bg-white', 'text-blue-600');

            tabRanking.classList.remove('hidden');
            tabPlayers.classList.add('hidden');
        });

        btnPlayers.addEventListener('click', () => {
            btnPlayers.classList.add('bg-blue-600', 'text-white');
            btnPlayers.classList.remove('bg-white', 'text-blue-600');

            btnRanking.classList.remove('bg-blue-600', 'text-white');
            btnRanking.classList.add('bg-white', 'text-blue-600');

            tabRanking.classList.add('hidden');
            tabPlayers.classList.remove('hidden');
        });

        searchInput.addEventListener('input', () => {
            const keyword = searchInput.value.toLowerCase();
            document.querySelectorAll('#players-list .player-item').forEach(item => {
                const name = item.textContent.toLowerCase();
                item.style.display = name.includes(keyword) ? 'flex' : 'none';
            });
        });

        document.getElementById('debug-correct').addEventListener('click', simulateCorrectGesture);
    });

    async function fetchAndRenderPlayers() {
        try {
            const response = await fetch("{{ route('gameRoom.getPlayers', $room->id) }}");
            if (!response.ok) throw new Error("Gagal mengambil data pemain");

            const data = await response.json();
            const players = data.players;

            // Render Ranking
            const rankingTab = document.getElementById('ranking-tab');
            rankingTab.innerHTML = '';
            players.sort((a, b) => b.score - a.score).forEach(player => {
                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-gray-100 border rounded px-3 py-2';
                div.innerHTML = `
                    <span class="font-medium text-gray-700 truncate">${player.name}</span>
                    <span class="text-sm text-gray-600">${player.score} pts</span>
                `;
                rankingTab.appendChild(div);
            });

            // Render Players
            const playersList = document.getElementById('players-list');
            playersList.innerHTML = '';
            players.sort((a, b) => a.user.name.localeCompare(b.user.name)).forEach(player => {
                const div = document.createElement('div');
                div.className = 'player-item flex justify-between items-center bg-gray-50 border rounded px-3 py-2';
                div.innerHTML = `
                    <span class="font-medium text-gray-700 truncate">${player.name}</span>
                    <span class="text-sm text-gray-500">${player.email}</span>
                `;
                playersList.appendChild(div);
            });

        } catch (error) {
            console.error('❌ Gagal memuat pemain:', error);
        }
    }

    function getRoomStatus() {
        fetch("{{ route('gameRoom.status', $room->id) }}")
            .then(response => response.json())
            .then(data => {
                // console.log(data);
                if (data.status === 'finished') {
                    redirectToFinish()
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function redirectToFinish() {
        window.location.href = "{{ route('gameRoom.finished', $room->id) }}";
    }

    setInterval(function () {
        getRoomStatus();
        fetchAndRenderPlayers();
    }, 3000);

    const helpBtn = document.getElementById('helpBtn');
    const helpModal = document.getElementById('helpModal');
    const cancelHelpBtn = document.getElementById('cancelHelpBtn');
    const confirmHelpBtn = document.getElementById('confirmHelpBtn');

    let helpUrl = "";

    helpBtn.addEventListener('click', (e) => {
        e.preventDefault();
        helpUrl = `/game-room/{{$room->id}}/help?user_id={{Auth::id()}}&game_room_id={{$room->id}}`;
        helpModal.classList.remove('hidden');
        helpModal.classList.add('flex');
    });

    cancelHelpBtn.addEventListener('click', () => {
        helpModal.classList.remove('flex');
        helpModal.classList.add('hidden');
    });

    confirmHelpBtn.addEventListener('click', async () => {
        helpModal.classList.remove('flex');
        helpModal.classList.add('hidden');

        try {
            const response = await fetch(helpUrl);
            if (response.ok) {
                const data = await response.json();
                console.log('berhasil meminta bantuan');
            } else {
                console.log('gagal meminta bantuan');
            }
        } catch (error) {
            console.error("Gagal:", error);

        }
    });

    const hintBtn = document.getElementById('hintBtn');
    const hintModal = document.getElementById('hintModal');
    const closeHint = document.getElementById('closeHint');

    hintBtn.addEventListener('click', () => {
        hintModal.classList.remove('hidden');
    });

    closeHint.addEventListener('click', () => {
        hintModal.classList.add('hidden');
    });

    hintModal.addEventListener('click', (e) => {
        if (e.target === hintModal) {
            hintModal.classList.add('hidden');
        }
    });

    document.getElementById('exitBtn').addEventListener('click', () => {
        event.preventDefault(); 
        const confirmExit = confirm("Apakah kamu yakin ingin keluar dari permainan?");
        if (confirmExit) {
            window.location.href = "/"; 
        }
    });

    // Deteksi

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
                    console.log(lastDetections);
                    
                }
            } catch (error) {
                console.error("Deteksi gagal:", error);
            } finally {
                isFetching = false;
            }
        }

        setInterval(fetchDetection, 1000);

        function renderLoop() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            // console.log(context, lastDetections, canvas.width, canvas.height, SNAPSHOT_WIDTH, SNAPSHOT_HEIGHT);
            
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

        if (currentQuestionIndex >= questions.length) return;
        const currentWord = questions[currentQuestionIndex].question_text.toUpperCase();
        const targetChar = currentWord[currentCharIndex];
        

        detections.forEach(d => {
            const [x1, y1, x2, y2] = d.bbox;
            const classId = d.class_id;
            const label = classLabels[classId] || classId;

            context.beginPath();
            context.rect(x1 * scaleX, y1 * scaleY, (x2 - x1) * scaleX, (y2 - y1) * scaleY);
            context.stroke();

            const confPercent = (d.confidence * 100).toFixed(1);
            context.fillText(`${label} (${confPercent}%)`, x1 * scaleX + 4, y1 * scaleY + 16);

            if (label === targetChar) {
                simulateCorrectGesture();
            }
        });
    }
</script>
@endsection
