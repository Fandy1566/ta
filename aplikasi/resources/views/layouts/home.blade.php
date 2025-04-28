@extends('layouts.template')

@section('body')
<body class="font-sans antialiased">
    <div class="min-h-screen overflow-x-hidden bg-gray-100">
        {{-- @include('layouts.navigation') --}}

        <!-- Page Heading -->
        <header class="bg-white shadow fixed top-0 left-0 right-0 z-50 w-full">
            <div class="h-16 flex items-center justify-between px-6 py-4 text-gray-800 border-b">
                {{-- Menu Navigasi --}}
                <div class="flex items-center gap-8">
                    <a href="{{ route('home.index') }}" class="text-lg font-semibold text-gray-700 hover:text-blue-600 transition duration-200">Home</a>
                    <a href="{{ route('home.index') }}" class="text-lg font-semibold text-gray-700 hover:text-blue-600 transition duration-200">Room</a>
                    <a href="{{ route('premium') }}" class="text-lg font-semibold text-gray-700 hover:text-blue-600 transition duration-200">Premium</a>
                </div>

                {{-- Info Pengguna --}}
                <div class="flex items-center gap-4">
                    <div class="flex flex-col text-end">
                        <p class="text-md font-semibold text-gray-800">
                            @php
                                if (Auth::user()) {
                                    echo Auth::user()->name;
                                } else {
                                    echo 'Tamu';
                                }
                                
                            @endphp
                        </p>
                        @php
                            if (Auth::user()) {
                                echo Auth::user()->is_premium() ?
                                    '<p class="text-sm text-yellow-500">
                                        Premium
                                    </p>'
                                :
                                    '<p class="text-sm text-yellow-800">
                                        Bronze
                                    </p>';
                            } else {
                                echo'<p class="text-sm text-yellow-800">
                                        Bronze
                                    </p>';
                            }
                        @endphp

                    </div>
                    <div class="w-12 h-12 bg-green-200 flex items-center justify-center rounded-full text-gray-700 text-sm font-semibold shadow-sm">
                        {{ Str::limit(Auth::user()->name ?? 'Tamu', 2, '') }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="px-10 pt-20"> <!-- Adding pt-20 to avoid header overlap -->
            @yield('main')
        </main>
    </div>
</body>
@endsection
