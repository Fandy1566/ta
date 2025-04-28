@extends('layouts.home')

@section('main')
<div class="flex flex-col gap-8 py-10">

    <div class="bg-white shadow-md rounded-lg p-6 flex flex-col gap-4">
        <h2 class="text-2xl font-bold text-gray-800">Ruangan: {{ $room->name }}</h2>
        <p class="text-gray-600">Kode Ruangan: <span class="font-mono">{{ $room->room_code }}</span></p> 
        <p class="text-gray-600">Jumlah Pemain: {{ $players->count()}}/{{ $room->max_players }}</p>
        <p class="text-gray-600">Status: {{ ucfirst($room->status) }}</p>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Pemain</h3>
        <div class="overflow-x-auto">
            <table id="players-table" class="min-w-full text-left border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4">Nama</th>
                        <th class="py-2 px-4">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $player->user->name }}</td>
                            <td class="py-2 px-4">{{ $player->score ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-4 text-center text-gray-500">Belum ada pemain.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 flex flex-col gap-4">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Setting Soal</h3>
        @if($questions->isNotEmpty())
            <ul class="list-disc pl-6">
                @foreach($questions as $index => $question)
                    <li class="mb-4">
                        <span class="font-semibold">Soal {{ $index + 1 }}:</span> {{ $question->question_text }}
                        <br>
                        <span class="text-sm text-gray-500">
                            Waktu: {{ $question->time_limit ? $question->time_limit . ' detik' : 'Tidak ada limit' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Belum ada soal yang ditambahkan.</p>
        @endif
    </div>

    <div class="flex justify-center mt-8">
        <form action="{{ route('gameRoom.startGame', $room->id) }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                Mulai Game
            </button>
        </form>
    </div>

</div>

{{-- Javascript Section --}}
<script>
    function fetchPlayers() {
        const roomId = {{ $room->id }};
        fetch(`/game-room/${roomId}/players`)
            .then(response => response.json())
            .then(data => {
                const playersTableBody = document.querySelector('#players-table tbody');
                playersTableBody.innerHTML = ''; // Clear the table

                // Populate the table with updated player data
                data.players.forEach(player => {
                    const row = document.createElement('tr');
                    row.classList.add('border-t');

                    const nameCell = document.createElement('td');
                    nameCell.classList.add('py-2', 'px-4');
                    nameCell.textContent = player.name;

                    const scoreCell = document.createElement('td');
                    scoreCell.classList.add('py-2', 'px-4');
                    scoreCell.textContent = player.score || 0;

                    row.appendChild(nameCell);
                    row.appendChild(scoreCell);

                    playersTableBody.appendChild(row);
                });
            });
    }

    // Fetch players every 5 seconds
    setInterval(fetchPlayers, 5000); // Update every 5 seconds
</script>

@endsection
