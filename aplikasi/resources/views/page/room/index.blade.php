@extends('layouts.home')

@section('main')
<div class="py-10 px-6 max-w-7xl mx-auto space-y-10">

    {{-- Section: Room yang Pernah Kamu Host --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Room Yang Pernah Kamu Host</h2>

        @if($hostedRooms->isEmpty())
            <p class="text-gray-500">Kamu belum pernah membuat room sebagai host.</p>
        @else
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($hostedRooms as $room)
                    <div class="p-4 bg-gray-50 border rounded-md shadow-sm">
                        <h3 class="font-bold text-lg">{{ $room->name }}</h3>
                        <p class="text-sm text-gray-600">Kode: <span class="font-mono">{{ $room->id }}</span></p>
                        <p class="text-sm text-gray-600">Status: {{ ucfirst($room->status) }}</p>
                        <a href="{{ route('gameRoom.host', $room->id) }}" class="mt-3 inline-block bg-indigo-500 text-white text-sm px-4 py-2 rounded hover:bg-indigo-600">
                            Kelola Room
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>


    {{-- Section: Room yang Pernah Dimasuki --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Room Yang Pernah Kamu Masuki</h2>

        @php
            $waitingRooms = $joinedRooms->where('status', 'waiting');
            $activeRooms = $joinedRooms->where('status', 'active');
            $finishedRooms = $joinedRooms->where('status', 'finished');
        @endphp

        {{-- Waiting Rooms --}}
        @if($waitingRooms->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Menunggu Dimulai</h3>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($waitingRooms as $room)
                        <div class="p-4 bg-gray-50 border rounded-md shadow-sm">
                            <h4 class="font-bold text-lg">{{ $room->name }}</h4>
                            <p class="text-sm text-gray-600">Kode: <span class="font-mono">{{ $room->id }}</span></p>
                            <a href="{{ route('gameRoom.join', $room->id) }}" class="mt-3 inline-block bg-yellow-500 text-white text-sm px-4 py-2 rounded hover:bg-yellow-600">
                                Masuk
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Active Rooms --}}
        @if($activeRooms->isNotEmpty())
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Sedang Berlangsung</h3>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($activeRooms as $room)
                        <div class="p-4 bg-gray-50 border rounded-md shadow-sm">
                            <h4 class="font-bold text-lg">{{ $room->name }}</h4>
                            <p class="text-sm text-gray-600">Kode: <span class="font-mono">{{ $room->id }}</span></p>
                            <a href="{{ route('gameRoom.join', $room->id) }}" class="mt-3 inline-block bg-green-500 text-white text-sm px-4 py-2 rounded hover:bg-green-600">
                                Masuk
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Finished Rooms --}}
        @if($finishedRooms->isNotEmpty())
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Telah Selesai</h3>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($finishedRooms as $room)
                        <div class="p-4 bg-gray-50 border rounded-md shadow-sm">
                            <h4 class="font-bold text-lg">{{ $room->name }}</h4>
                            <p class="text-sm text-gray-600">Kode: <span class="font-mono">{{ $room->id }}</span></p>
                            <p class="mt-2 text-sm text-gray-500 italic">Room telah selesai</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Jika semua kosong --}}
        @if($joinedRooms->isEmpty())
            <p class="text-gray-500">Kamu belum pernah masuk ke ruangan manapun.</p>
        @endif
    </div>


    {{-- Section: Cari Room Publik --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Room Publik Aktif</h2>

        <input 
            type="text" 
            id="searchInput" 
            placeholder="Cari berdasarkan nama ruangan..." 
            class="w-full mb-4 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
        >

        <div id="publicRoomList" class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
            @forelse ($publicRooms as $room)
                <div class="p-4 bg-gray-50 border rounded-md shadow-sm">
                    <h3 class="font-bold text-lg">{{ $room->name }}</h3>
                    <p class="text-sm text-gray-600">Kode: <span class="font-mono">{{ $room->id }}</span></p>
                    <p class="text-sm text-gray-600">Pemain: {{ $room->current_players }}/{{ $room->max_players }}</p>
                    <p class="text-sm text-gray-600">Status: {{ ucfirst($room->status) }}</p>

                    <a href="{{ route('gameRoom.join', $room->id) }}" class="mt-3 inline-block bg-green-500 text-white text-sm px-4 py-2 rounded hover:bg-green-600">
                        Gabung
                    </a>
                </div>
            @empty
                <p class="text-gray-500">Tidak ada room publik aktif saat ini.</p>
            @endforelse
        </div>
    </div>

</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const publicRoomList = document.getElementById('publicRoomList');

    searchInput.addEventListener('keyup', function () {
        const filter = searchInput.value.toLowerCase();
        const rooms = publicRoomList.querySelectorAll('div.p-4');

        rooms.forEach(room => {
            const roomName = room.querySelector('h3').textContent.toLowerCase();
            room.style.display = roomName.includes(filter) ? '' : 'none';
        });
    });
</script>
@endsection
