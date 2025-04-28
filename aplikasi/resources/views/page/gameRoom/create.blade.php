@extends('layouts.home')

@section('main')

@if (session('success'))
    <div class="mb-6 w-fit mx-auto bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded relative" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 w-fit mx-auto bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded relative" role="alert">
        <strong class="font-bold">Gagal!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<form action="{{ route('gameRoom.store') }}" method="post">
    @csrf
    <div class="mx-auto px-8 py-8 bg-white shadow rounded-md w-fit flex gap-10" x-data="{
        questions: [
            { question_text: '', time_limit: '' }
        ],
        addQuestion() {
            this.questions.push({ question_text: '', time_limit: '' });
        },
        removeQuestion(index) {
            this.questions.splice(index, 1);
        }
    }">

        {{-- Bagian Kiri: Detail Room --}}
        <div class="flex flex-col gap-4">
            <h2 class="font-bold text-lg mb-2">Detail Ruangan</h2>

            <div>
                <p class="text-sm mb-1">Nama Ruangan</p>
                <input 
                    class="border border-gray-300 rounded-md p-2 w-full" 
                    type="text" 
                    name="name" 
                    required
                >
            </div>

            <div>
                <p class="text-sm mb-1">Jumlah Maksimal Pemain</p>
                <input 
                    class="border border-gray-300 rounded-md p-2 w-full" 
                    type="number" 
                    name="max_players" 
                    min="2" max="100"
                    required
                >
                @unless(Auth::user()->is_premium())
                    <p class="text-xs text-red-500 mt-1">Hanya pengguna premium yang bisa membuat room dengan lebih dari 15 Pemain.</p>
                @endunless
            </div>

            <h3 class="font-semibold text-md mt-4">Fitur Premium</h3>

            <div>
                <p class="text-sm mb-1">Kode Ruangan</p>
                <input 
                    class="border border-gray-300 rounded-md p-2 w-full" 
                    type="text" 
                    name="custom_code" 
                    placeholder="Contoh: KUISHEBAT123"
                    maxlength="12"
                    {{ Auth::user()->is_premium() ? '' : 'disabled' }}
                >
                @unless(Auth::user()->is_premium())
                    <p class="text-xs text-red-500 mt-1">Hanya pengguna premium yang bisa kustom kode ruangan.</p>
                @endunless
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_private" id="is_private" value="1" {{Auth::user()->is_premium() ? "" : 'disabled'}}>
                <label for="is_private" class="text-sm">Ruangan Private?</label>
            </div>

            {{-- <div class="flex items-center gap-2">
                <input type="checkbox" name="power_up" id="power_up" value="1" {{Auth::user()->is_premium ? "" : 'disabled'}}>
                <label for="power_up" class="text-sm">Power Up?</label>
            </div> --}}

            <button type="submit" class="bg-green-500 rounded-md py-3 px-4 text-white mt-6 hover:bg-green-600">
                Buat Ruangan
            </button>
        </div>

        {{-- Bagian Kanan: Soal --}}
        <div class="flex flex-col gap-4 w-96">
            <div class="">
                <h3 class="font-bold text-lg mb-2">Soal</h3>
                <p class="text-sm text-gray-600">Kosongkan waktu kalau ingin soal tidak ada waktu limit</p>
            </div>
            <template x-for="(question, index) in questions" :key="index">
                    <div class="flex flex-col gap-2 mb-4 border p-4 rounded-md relative bg-gray-50">
                        <input 
                        type="text" 
                        :name="'question_text[' + index + ']'" 
                        x-model="question.question_text"
                        class="border border-gray-300 rounded-md p-2 w-full" 
                        :placeholder="'Tulis soal ke-' + (index + 1)" 
                        required
                    >
                    <input 
                        type="number" 
                        :name="'time_limit[' + index + ']'" 
                        x-model="question.time_limit"
                        class="border border-gray-300 rounded-md p-2 w-full" 
                        placeholder="Waktu limit soal (detik)" 
                        min="5" 
                    >
                    <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600" @click="removeQuestion(index)">×</button>
                </div>
            </template>

            <button type="button" class="bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600" @click="addQuestion()">
                + Tambah Soal
            </button>
        </div>

    </div>
</form>

{{-- Jangan lupa load Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
