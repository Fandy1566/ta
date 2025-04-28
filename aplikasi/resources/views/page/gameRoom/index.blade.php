@extends('layouts.template')

@section('body')
<div class="h-screen flex">
    {{-- Kamera Section --}}
    <div id="camera" class="bg-gray-200 h-screen flex-grow relative">
        <video id="webcam" autoplay muted playsinline class="absolute top-0 left-0 w-full h-full object-cover z-0"></video>
        <canvas id="canvas" class="absolute top-0 left-0 w-full h-full z-10"></canvas>

        {{-- Tombol bawah --}}
        <div class="absolute bottom-2 left-0 right-0 w-fit mx-auto bg-white p-3 rounded-full flex gap-4 shadow-md">
            <button class="bg-gray-400 rounded-full w-12 h-12 text-white font-semibold">Help</button>
            <button class="bg-gray-400 rounded-full w-12 h-12 text-white font-semibold">Hint</button>
            <button class="bg-gray-400 rounded-full w-12 h-12 text-white font-semibold">Exit</button>
        </div>

        {{-- Timer --}}
        <div id="timer" class="absolute top-2 left-1/2 transform -translate-x-1/2 bg-white p-3 rounded-md flex gap-4 shadow-md">
            <span class="text-lg font-bold text-gray-800" id="timer-span"></span>
        </div>

        {{-- Peragakan kata --}}
        <div class="absolute top-2 right-2 bg-white p-3 rounded-md flex flex-col gap-2 shadow-md">
            <p class="text-sm font-semibold">Peragakan:</p>
            <div id="word-display" class="flex gap-1">
                
            </div>
        </div>

    </div>

    {{-- Detail Section --}}
    <div id="detail" class="bg-white h-screen w-[300px] p-4 flex flex-col gap-4 shadow-lg overflow-y-auto">
        {{-- User Info --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-slate-300 rounded-full"></div>
            <div class="flex flex-col">
                <p class="font-semibold text-gray-800">{{Auth::user()->name}}</p>
                @php
                            if (Auth::user()) {
                                echo Auth::user()->is_premium ?
                                    '<p class="text-sm text-yellow-500">
                                        Premium
                                    </p>'
                                :
                                    '<p class="text-sm text-yellow-800">
                                        Bronze
                                    </p>';
                            } else {
                                echo'<p class="text-sm text-yellow-800">
                                        Bronze
                                    </p>';
                            }
                        @endphp
            </div>
        </div>
        <hr>

        {{-- Menu Ranking --}}
        <div class="flex flex-col gap-2">
            <div class="flex gap-1 mb-2">
                <button class="flex-grow border rounded-md py-1 font-semibold bg-gray-200">Ranking</button>
                <button class="flex-grow border rounded-md py-1 font-semibold">Pemain</button>
            </div>

            {{-- Ranking List --}}
            <div class="flex flex-col gap-2">
                @foreach ($players->sortByDesc('score') as $player)
                    <div class="flex justify-between items-center border p-2 rounded">
                        <span class="font-semibold">{{ $player->user->name }}</span>
                        <span class="text-sm text-gray-600">{{ $player->score }} pts</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Javascript Section --}}
<script>
    // Mengambil data questions dari blade ke dalam JavaScript
    const questions = @json($questions);

    let currentQuestionIndex = 0;
    let timeLeft = questions[currentQuestionIndex].time_limit; // Waktu untuk pertanyaan pertama
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
        wordDisplay.innerHTML = ''; // Clear the previous word

        const questionText = questions[currentQuestionIndex].question_text;
        const chars = questionText.toUpperCase().split(''); // Ganti str_split dengan split('')

        chars.forEach((char, index) => {
            const charDiv = document.createElement('div');
            charDiv.classList.add('w-10', 'h-10', 'p-1', 'border', 'bg-gray-300', 'border-gray-500', 'text-lg', 'grid', 'place-items-center', 'rounded');
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
</script>
@endsection
