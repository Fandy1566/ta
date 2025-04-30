@extends('layouts.home')

@section('main')
<div class="flex justify-center items-center py-10 bg-gray-100">
    <div class="px-8 py-8 bg-white shadow-lg rounded-md w-fit flex flex-col items-center gap-6">
        <h2 class="text-2xl font-semibold text-gray-700">Yah, gamenya sudah selesai!</h2>
        <p class="text-gray-500 text-center max-w-sm">Klik keluar untuk memilih room lainnya</p>

        {{-- Tampilkan skor hanya jika pemain sudah bermain --}}
        @if(isset($score) && $score !== null)
            <div class="text-center bg-gray-50 px-6 py-4 rounded-md border border-gray-200">
                <p class="text-lg text-gray-700 font-medium mb-1">Skor Akhir Kamu:</p>
                <p class="text-3xl font-bold text-green-600">{{ $score }}</p>
            </div>
        @endif

        {{-- Tombol Keluar --}}
        <a href="{{ route('home.index') }}" class="bg-blue-500 text-white px-6 py-3 rounded-md shadow-md hover:bg-blue-600 transition duration-300">
            Keluar
        </a>
    </div>
</div>
@endsection
