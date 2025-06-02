@extends('layouts.home')

@section('main')
<div class="flex flex-col items-center gap-8 py-10">

    {{-- Informasi Room --}}
    <div class="px-8 py-6 bg-white shadow-md rounded-xl text-center w-fit">
        <h2 class="text-2xl font-bold">Room: {{ $room->name }}</h2>
    </div>

    {{-- Pesan Menunggu --}}
    <div class="px-8 py-6 bg-white shadow-md rounded-xl text-center w-fit animate-pulse">
        <p class="text-lg font-semibold text-gray-700">Tunggu sebentar ya, host sedang menyiapkan game...</p>
    </div>

    {{-- List Pemain --}}
    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-4 w-fit mx-auto" id="player-list">
        @forelse ($players as $player)
            <div class="flex flex-col items-center">
                <div class="w-[60px] h-[60px] bg-green-200 flex items-center justify-center rounded-full text-gray-700 text-sm font-semibold shadow-sm">
                    {{ Str::limit($player->user->name, 2, '') }}
                </div>
                <span class="mt-1 text-xs text-gray-700 text-center w-[70px] truncate">
                    {{ $player->user->name }}
                </span>
            </div>
        @empty
            <p class="col-span-full text-gray-500">Belum ada pemain bergabung.</p>
        @endforelse
    </div>

    {{-- Tombol Keluar --}}
    <form action="{{ route('gameRoom.leave', $room->id) }}" method="POST" onsubmit="return confirm('Yakin ingin keluar dari room?')">
        @csrf
        <button type="submit" class="mt-8 px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg shadow">
            Keluar Room
        </button>
    </form>

</div>

<script>
    setInterval(function () {
        getRoomStatus();
        getPlayerInRoom();
    }, 3000); // Cek tiap 3 detik

    function getRoomStatus() {
        fetch("{{ route('gameRoom.status', $room->id) }}")
            .then(response => response.json())
            .then(data => {
                // console.log(data);
                if (data.status === 'active') {
                    redirectToGame()
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function redirectToGame() {
        window.location.href = "{{ route('gameRoom.index', $room->id) }}";
    }

    function getPlayerInRoom() {
        fetch("{{ route('gameRoom.getPlayers', $room->id) }}")
            .then(response => response.json())
            .then(data => {
                console.log(data);
                renderPlayerList(data);
            })
            .catch(error => console.error('Error:', error));
    }

    function renderPlayerList(data) {
        const playerList = document.getElementById('player-list');
        playerList.innerHTML = ''; // Kosongkan daftar lama

        if (!data || data.length === 0) {
            playerList.innerHTML = `
                <p class="col-span-full text-gray-500">Belum ada pemain bergabung.</p>
            `;
            return;
        }

        data.players.forEach(player => {
            const initial = player.name.substring(0, 2).toUpperCase();
            const playerHTML = `
                <div class="flex flex-col items-center">
                    <div class="w-[60px] h-[60px] bg-green-200 flex items-center justify-center rounded-full text-gray-700 text-sm font-semibold shadow-sm">
                        ${initial}
                    </div>
                    <span class="mt-1 text-xs text-gray-700 text-center w-[70px] truncate">
                        ${player.name}
                    </span>
                </div>
            `;
            playerList.insertAdjacentHTML('beforeend', playerHTML);
        });
    }
</script>
@endsection
