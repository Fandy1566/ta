@extends('layouts.home')

@section('main')
<div class="flex justify-center items-center bg-gray-100 py-10">
    <div class="bg-white w-full max-w-xl p-6 shadow-xl rounded-lg mx-auto">
        {{-- Judul dan Deskripsi --}}
        <h2 class="text-3xl font-semibold text-gray-800 text-center mb-6">Keuntungan Berlangganan Premium</h2>
        <p class="text-center text-gray-600 mb-8">Yuk mulai langganan premium mulai dari <span class="font-semibold text-green-500">Rp. 50.000/bulan</span> untuk mendukung aplikasi dan dapatkan berbagai keuntungan menarik!</p>

        {{-- Keuntungan Premium --}}
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-gray-700">Buat room tanpa batas</p>
            </div>
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-gray-700">Jumlah player bisa lebih dari 15</p>
            </div>
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-gray-700">Membuat room private</p>
            </div>
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <p class="text-gray-700">Kustomisasi kode ruangan</p>
            </div>
        </div>

        {{-- Kontak --}}
        <p class="text-center text-gray-600 mt-6">Hubungi WA: <span class="font-semibold text-blue-500">0812-7223-3039</span> untuk berlangganan premium</p>
    </div>
</div>
@endsection
