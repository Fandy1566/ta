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
                        <th class="py-2 px-4">Bantuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $player->user->name }}</td>
                            <td class="py-2 px-4">{{ $player->score ?? 0 }}</td>
                            <td class="py-2 px-4">
                                @if($player->help)
                                    <span class="text-red-500 font-semibold">Ya</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-500">Belum ada pemain.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 flex flex-col gap-4" x-data="{ showEdit: false, questionId: null, questionText: '', timeLimit: 0 }">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Setting Soal</h3>
    
        @if($room->status === 'waiting')
            <form action="{{ route('question.store', $room->id) }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label class="block text-gray-700">Pertanyaan</label>
                    <input type="text" name="question_text" class="w-full border rounded px-3 py-2" required placeholder="Masukkan Pertanyaan baru">
                </div>
                <div>
                    <label class="block text-gray-700">Waktu (detik)</label>
                    <input type="number" name="time_limit" class="w-full border rounded px-3 py-2" min="0" placeholder="Masukkan Waktu limit">
                </div>
                <button type="submit" class="self-start bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Tambahkan Soal
                </button>
            </form>
        @endif
    
        @if($questions->isNotEmpty())
            <ul class="list-disc pl-6 mt-4">
                @foreach($questions as $index => $question)
                    <li class="mb-4 flex justify-between items-start gap-4">
                        <div>
                            <span class="font-semibold">Soal {{ $index + 1 }}:</span> {{ $question->question_text }}
                            <br>
                            <span class="text-sm text-gray-500">
                                Waktu: {{ $question->time_limit ? $question->time_limit . ' detik' : 'Tidak ada limit' }}
                            </span>
                        </div>
                        @if($room->status === 'waiting')
                            <div class="flex gap-2">
                                <button 
                                    type="button"
                                    class="text-blue-500 hover:text-blue-700 text-sm font-semibold"
                                    @click="showEdit = true; questionId = {{ $question->id }}; questionText = '{{ addslashes($question->question_text) }}'; timeLimit = {{ $question->time_limit ?? 0 }}"
                                >
                                    Edit
                                </button>
    
                                <form action="{{ route('question.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    
        <!-- Modal -->
        <div 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            x-show="showEdit"
            x-cloak
        >
            <div class="bg-white rounded-lg p-6 w-96">
                <h3 class="text-xl font-bold mb-4">Edit Soal</h3>
                <form :action="`/question/${questionId}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700">Pertanyaan</label>
                        <input type="text" name="question_text" x-model="questionText" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Waktu (detik)</label>
                        <input type="number" name="time_limit" x-model="timeLimit" class="w-full border rounded px-3 py-2" min="0">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showEdit = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    

    <div class="flex flex-col items-center gap-4 mt-8">
        @if ($room->status === 'waiting')
            <form action="{{ route('gameRoom.startGame', $room->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                    Mulai Game
                </button>
            </form>
        @elseif ($room->status === 'active')
            <form action="{{ route('gameRoom.finished', $room->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                    Selesaikan Room
                </button>
            </form>
        @elseif ($room->status === 'finished')
            <p class="text-gray-600 text-lg font-semibold">Room telah selesai</p>
            <a href="{{ route('room.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                Kembali ke Home
            </a>
        @endif
    </div>
    

</div>

<script>
function fetchAndRenderPlayers() {
    fetch("{{ route('gameRoom.getPlayers', $room->id) }}")
        .then(response => response.json())
        .then(data => {
            console.log(data);

            const playersTableBody = document.querySelector('#players-table tbody');
            playersTableBody.innerHTML = '';

            if (!Array.isArray(data.players) || data.players.length === 0) {
                const emptyRow = document.createElement('tr');
                const emptyCell = document.createElement('td');
                emptyCell.setAttribute('colspan', 3);
                emptyCell.classList.add('py-4', 'text-center', 'text-gray-500');
                emptyCell.textContent = 'Belum ada pemain.';
                emptyRow.appendChild(emptyCell);
                playersTableBody.appendChild(emptyRow);
                return;
            }

            data.players.forEach(player => {
                const row = document.createElement('tr');
                row.classList.add('border-t');

                const nameCell = document.createElement('td');
                nameCell.classList.add('py-2', 'px-4');
                nameCell.textContent = player.name;

                const scoreCell = document.createElement('td');
                scoreCell.classList.add('py-2', 'px-4');
                scoreCell.textContent = player.score || 0;

                const helpCell = document.createElement('td');
                helpCell.classList.add('py-2', 'px-4');
                helpCell.innerHTML = player.help ? 
                    `<span class="text-red-500 font-semibold">Ya</span>` : 
                    `<span class="text-gray-400">-</span>`;

                row.appendChild(nameCell);
                row.appendChild(scoreCell);
                row.appendChild(helpCell);

                playersTableBody.appendChild(row);
            });
        });
}


    setInterval(fetchAndRenderPlayers, 2000);
</script>

@endsection
