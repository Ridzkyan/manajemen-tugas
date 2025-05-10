<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TaskFlow') }}</title>

    <!-- Fonts and Styles -->
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<div id="app" class="d-flex">
    {{-- Sidebar --}}
    <aside class="bg-dark text-white p-3" style="width: 250px; min-height: 100vh;">
        <h5 class="mb-4">NAMA LOGO</h5>

        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.dashboard') }}"
                   class="nav-link {{ Route::is('dosen.dashboard') ? 'text-warning' : 'text-white' }}">
                    📊 Dashboard
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('dosen.materikelas') }}"
                   class="nav-link {{ Route::is('dosen.materikelas') ? 'text-warning' : 'text-white' }}">
                    📁 Materi & Kelas
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('dosen.tugas.index', $kelasPertama->id ?? 1) }}"
                   class="nav-link {{ Route::is('dosen.tugas.*') ? 'text-warning' : 'text-white' }}">
                    📝 Tugas & Ujian
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('dosen.komunikasi') }}"
                   class="nav-link {{ Route::is('dosen.komunikasi') ? 'text-warning' : 'text-white' }}">
                    💬 Komunikasi
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('dosen.kelas.index') }}"
                   class="nav-link {{ Route::is('dosen.kelas.*') ? 'text-warning' : 'text-white' }}">
                    🔧 Kelola Kelas
                </a>
            </li>

            <li class="nav-item mb-2">
    <a href="{{ route('dosen.rekap.nilai') }}"
       class="nav-link {{ Route::is('dosen.rekap.nilai') ? 'text-warning' : 'text-white' }}">
        📑 Rekap Nilai
    </a>
</li>

        </ul>

        <form action="{{ route('dosen.logout') }}" method="POST" class="mt-4">
            @csrf
            <button class="btn btn-sm btn-outline-light w-100">🔓 Logout</button>
        </form>
    </aside>

    {{-- Konten Utama --}}
    <main class="flex-grow-1 p-4">
        @yield('content')
    </main>
</div>

@yield('scripts')
@stack('scripts')
</body>
</html>
