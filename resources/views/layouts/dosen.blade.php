<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TaskFlow') }}</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #fef9f4;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        #app {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            background-color: #00838f;
            color: white;
            width: 240px;
            min-height: 100vh;
            padding: 20px;
        }

        .sidebar h5 {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar .nav-link {
            color: white;
            font-weight: 600;
            border-radius: 30px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            transition: background 0.3s ease;
            font-size: 15px;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active, .sidebar .nav-link.text-warning {
            background-color: white !important;
            color: #f5a04e !important;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background-color: #f5a04e;
            padding: 15px 30px;
            border-radius: 0 0 12px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .topbar h5 {
            margin: 0;
            color: white;
            font-weight: bold;
        }

        .content-wrapper {
            padding: 30px;
            overflow-y: auto;
            flex-grow: 1;
        }
    </style>
</head>
<body>
<div id="app">
    {{-- Sidebar --}}
    <aside class="sidebar">
        <h5>TASKFLOW</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.dashboard') }}" class="nav-link {{ Route::is('dosen.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.materi_kelas.index') }}" class="nav-link {{ Route::is('dosen.materi_kelas.index') ? 'active' : '' }}">
                    <i class="fas fa-folder"></i> Materi & Kelas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.tugas_ujian.pilih_kelas', $kelasPertama->id ?? 1) }}" class="nav-link {{ Route::is('dosen.tugas_ujian.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i> Tugas & Ujian
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.komunikasi') }}" class="nav-link {{ Route::is('dosen.komunikasi') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i> Komunikasi
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.kelola_kelas.index') }}" class="nav-link {{ Route::is('dosen.kelola_kelas.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Kelola Kelas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.rekap_nilai.index') }}" class="nav-link {{ Route::is('dosen.rekap_nilai') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Rekap Nilai
                </a>
            </li>
            <li class="nav-item mb-2">
    <a href="{{ route('dosen.pengaturan.profil') }}" class="nav-link {{ Route::is('dosen.pengaturan.profil') ? 'active' : '' }}">
        <i class="fas fa-user-cog"></i> Pengaturan Profil
    </a>
</li>
</ul>
</aside>

    {{-- Main --}}
    <div class="main-content">
        {{-- Topbar --}}
        <div class="topbar">
            <h5>Dashboard Dosen</h5>
            <div class="text-white">
                <strong>{{ Auth::guard('dosen')->user()->name ?? 'Nama Dosen' }}</strong><br>
                <small>Dosen</small>
            </div>
        </div>

        {{-- Konten --}}
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
@stack('scripts')
</body>
</html>
