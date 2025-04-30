@extends('layouts.template')

@section('body')
<div class="h-screen flex overflow-hidden">

    {{-- Kamera Section --}}
    <div id="camera" class="flex-1 bg-black relative">
        <video id="webcam" autoplay muted playsinline class="absolute inset-0 w-full h-full object-cover z-0"></video>
        <canvas id="canvas" class="absolute inset-0 w-full h-full z-10"></canvas>

        {{-- Tombol kontrol bawah --}}
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-4 bg-white/80 backdrop-blur-md p-2 rounded-full shadow-lg z-20">
            <button class="w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold">?</button>
            <button class="w-12 h-12 rounded-full bg-green-600 hover:bg-green-700 text-white font-bold">💡</button>
            <button class="w-12 h-12 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold">⏻</button>
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

            {{-- Player List with Search --}}
            <div id="players-tab" class="space-y-2 hidden">
                <input type="text" id="player-search" placeholder="Cari pemain..." class="w-full px-3 py-2 border rounded-md text-sm">
                
                <div id="players-list" class="space-y-2">
                    @foreach ($players->sortBy(fn($a, $b) => strcmp($a->user->name, $b->user->name)) as $player)
                        <div class="player-item flex justify-between items-center bg-gray-50 border rounded px-3 py-2">
                            <span class="font-medium text-gray-700 truncate">{{ $player->user->name }}</span>
                            <span class="text-sm text-gray-500">{{ $player->user->email }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
    const questions = @json($questions);
    let currentQuestionIndex = 0;
    let timeLeft = questions[currentQuestionIndex].time_limit;
    const timerElement = document.getElementById('timer-span');
    const wordDisplay = document.getElementById('word-display');

    function startCamera() {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                document.getElementById('webcam').srcObject = stream;
            })
            .catch((error) => {
                console.error('Camera error:', error);
            });
    }

    function startTimer() {
        if (timeLeft > 0 && timerElement) {
            setInterval(() => {
                if (timeLeft > 0) {
                    timeLeft--;
                    timerElement.textContent = timeLeft + ' detik';
                }
            }, 1000);
        }
    }

    function displayWord() {
        wordDisplay.innerHTML = '';
        const chars = questions[currentQuestionIndex].question_text.toUpperCase().split('');
        chars.forEach((char) => {
            const charDiv = document.createElement('div');
            charDiv.classList.add('w-10', 'h-10', 'bg-gray-300', 'border', 'rounded-md', 'grid', 'place-items-center', 'text-lg', 'font-semibold');
            charDiv.textContent = char;
            wordDisplay.appendChild(charDiv);
        });
    }

    function main() {
        startCamera();
        startTimer();
        displayWord();
    }

    document.addEventListener('DOMContentLoaded', main);

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
    });

</script>
@endsection
