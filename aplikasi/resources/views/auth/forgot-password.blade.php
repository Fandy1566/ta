@extends('layouts.template')

@section('body')
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex justify-center items-center">
        <div class="bg-white p-8 shadow-lg rounded-md w-full sm:w-96">
            <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Forgot Your Password?</h2>

            <!-- Forgot Password Form -->
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Enter your email" required>
                </div>

                <button type="submit" class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-200">Send Password Reset Link</button>
            </form>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Remembered your password? <a href="{{ route('login') }}" class="text-green-500 hover:underline">Login here</a></p>
            </div>
        </div>
    </div>
</body>
@endsection
