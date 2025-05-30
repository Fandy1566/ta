@extends('layouts.home')
@section('main')
<div class="flex flex-col gap-8 py-10">

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

    {{-- Ucapan Selamat Datang --}}
    <div class="">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Halo {{Auth::user()? Auth::user()->name : 'Tamu'}}! Senang berjumpa lagi 😊</h1>
        <p class="text-lg text-gray-600">Selamat datang kembali di dashboard. Apa yang ingin kamu lakukan hari ini?</p>
    </div>

    {{-- Bagian untuk Membuat Room --}}
    <div class="flex gap-8 mt-8">
        <div class="px-8 py-6 bg-white shadow-lg rounded-md w-full sm:w-80 flex flex-col items-center justify-center gap-4">
            <p class="text-xl font-semibold text-gray-800">Mau test aplikasi?</p>
            <a href="{{ route('test.index') }}" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition duration-200">Klik disini</a>
        </div>

        <div class="px-8 py-6 bg-white shadow-lg rounded-md w-full sm:w-80 flex flex-col items-center justify-center gap-4">
            <p class="text-xl font-semibold text-gray-800">Belum ada room?</p>
            <a href="{{ route('gameRoom.create') }}" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition duration-200">Buat Room</a>
        </div>
        
        {{-- Bagian untuk Masukkan Kode Ruangan --}}
        <div class="px-8 py-6 bg-white shadow-lg rounded-md w-full sm:w-80 flex flex-col gap-4">
            <p class="text-xl font-semibold text-gray-800">Ada kode ruangan? Masukkan di sini!</p>
        
            <form action="{{ route('room.joinByCode') }}" method="GET" class="flex flex-col gap-4">
                <input 
                    class="border-2 border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-green-500" 
                    type="text" 
                    name="room_code" 
                    id="room_code" 
                    placeholder="Masukkan kode ruangan" 
                    required
                >
                <button 
                    type="submit" 
                    class="bg-blue-500 text-white py-2 px-4 rounded-md mt-2 hover:bg-blue-600 transition duration-200"
                >
                    Masukkan Kode
                </button>
            </form>
        </div>
        
        
    </div>

    {{-- Bagian Room Publik --}}
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Room Publik</h2>
    
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rooms as $room)
                <div class="bg-white shadow-lg rounded-md flex flex-col overflow-hidden">
                    <div class="bg-green-300 grow flex items-center justify-center h-32">
                        {{-- Bisa kasih gambar/banner room di sini kalau mau --}}
                        <span class="text-lg font-bold text-white">{{ $room->name }}</span>
                    </div>
                    <div class="flex h-16 items-center px-4 justify-between bg-gray-50 rounded-b-md">
                        <div class="flex-col text-left">
                            <p class="font-semibold text-gray-800">{{ $room->name }}</p>
                            <p class="text-gray-600">{{ $room->user->name }}</p>
                        </div>
                        <a href="{{ route('gameRoom.join', $room->id) }}" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                            Join ({{ $room->current_players }}/{{ $room->max_players }})
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">Belum ada room publik yang tersedia.</p>
            @endforelse
        </div>
    </div>    
</div>
@endsection
