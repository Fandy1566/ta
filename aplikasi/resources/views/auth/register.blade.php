@extends('layouts.template')

@section('body')
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex justify-center items-center">
        <div class="bg-white p-8 shadow-lg rounded-md w-full sm:w-96">
            <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Buat akun</h2>

            @if ($errors->any())
                <div class="mb-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Ups!</strong> Ada yang salah:
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Register Form -->
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-semibold text-gray-700">Nama lengkap</label>
                    <input type="text" name="name" id="name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan nama lengkapmu" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan emailmu" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Masukkan password" required>
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Konfirmasi password yang dibuat" required>
                </div>

                <button type="submit" class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-200">Daftar</button>
            </form>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" class="text-green-500 hover:underline">Login disini</a></p>
            </div>
        </div>
    </div>
</body>
@endsection
