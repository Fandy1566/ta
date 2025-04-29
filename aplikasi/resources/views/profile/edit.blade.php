@extends('layouts.home')

@section('main')
<div class="max-w-xl mx-auto py-10 px-6 bg-white shadow-md rounded-xl space-y-8">

    {{-- Judul --}}
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Profil</h2>
    </div>

    {{-- Informasi Premium --}}
    <div class="bg-gray-50 p-5 rounded-md border border-gray-200 space-y-2">
        <p class="text-sm text-gray-600">Status Akun:</p>
        <p class="text-xl font-semibold {{ auth()->user()->is_premium() ? 'text-yellow-600' : 'text-gray-700' }}">
            {{ auth()->user()->is_premium() ? 'Premium' : 'Bronze' }}
        </p>

        @if(auth()->user()->is_premium())
            <p class="text-sm text-gray-600">
                Berlaku hingga: 
                <span class="font-medium text-gray-800">
                    {{ \Carbon\Carbon::parse(auth()->user()->premium_until)->translatedFormat('d F Y') }}
                </span>
            </p>
        @else
            <a href="{{ route('premium') }}" class="inline-block mt-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-md shadow">
                Beli Premium
            </a>
        @endif
    </div>

    {{-- Form Edit Profil --}}
    <div>
        <h3 class="text-2xl font-semibold text-gray-800 mb-4">Edit Profil</h3>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error('name')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error('email')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Password Baru <span class="text-sm text-gray-400">(opsional)</span></label>
                <input type="password" name="password"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                @error('password')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md shadow">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
