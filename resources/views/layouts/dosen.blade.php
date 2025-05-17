<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TaskFlow') }}</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        html, body {
            background-color: #fef9f4;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
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
            transition: margin 0.3s ease;
        }

        .sidebar.hidden {
            margin-left: -240px;
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

        .sidebar .nav-link.active,
        .sidebar .nav-link.text-warning {
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
            transition: margin 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
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

        .transition-rotate {
            transition: transform 0.3s ease;
        }

        .rotate-90 {
            transform: rotate(90deg);
        }

        .content-wrapper {
            padding: 30px;
            overflow-y: auto;
            flex-grow: 1;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                z-index: 1000;
                height: 100%;
                left: 0;
                top: 0;
            }

            .topbar {
                gap: 10px;
            }

            .content-wrapper {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div id="app">
    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <h5>TASKFLOW</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.dashboard') }}" class="nav-link {{ Route::is('dosen.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.materi_kelas.index') }}" class="nav-link {{ request()->is('dosen/materi-kelas*') ? 'active' : '' }}">
                    <i class="fas fa-folder"></i> Materi/Kelas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.tugas_ujian.pilih_kelas', $kelasPertama->id ?? 1) }}" class="nav-link {{ Route::is('dosen.tugas_ujian.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i> Tugas/Ujian
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
                <a href="{{ route('dosen.rekap_nilai.index') }}" 
                class="nav-link {{ Route::is('dosen.rekap_nilai.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Rekap Nilai
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('dosen.pengaturan') }}" class="nav-link {{ request()->is('dosen/pengaturan*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i> Pengaturan
                </a>
            </li>
        </ul>
    </aside>

    {{-- Main --}}
    <div class="main-content" id="mainContent">
        {{-- Topbar --}}
        <div class="topbar">
            {{-- Toggle Sidebar --}}
            <button id="toggleSidebar" class="btn border-0 bg-transparent p-0 me-3">
                <i id="toggleIcon" class="fas fa-bars fa-lg transition-rotate"></i>
            </button>

            {{-- Teks kiri --}}
            <h5 class="mb-0 me-3">Dashboard Dosen</h5>

            {{-- FORM SEARCH DI TENGAH --}}
            <form action="{{ route('dosen.search') }}" method="GET" class="mx-auto d-none d-md-block" style="width: 100%; max-width: 450px;">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Cari konten..." required>
                    <button class="btn btn-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            {{-- Profil kanan --}}
            <div class="text-white text-end ms-auto">
                <strong>{{ Auth::guard('dosen')->user()->name ?? 'Dosen' }}</strong><br>
                <small>Dosen</small>
            </div>
        </div>

        {{-- Konten Halaman --}}
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleSidebar');
        const toggleIcon = document.getElementById('toggleIcon');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        toggleBtn?.addEventListener('click', function () {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
            toggleIcon.classList.toggle('rotate-90');
        });

        // Auto close sidebar on mobile when clicking outside
        document.addEventListener('click', function (event) {
            const isMobile = window.innerWidth <= 768;
            if (
                isMobile &&
                sidebar &&
                !sidebar.contains(event.target) &&
                !toggleBtn.contains(event.target) &&
                !sidebar.classList.contains('hidden')
            ) {
                sidebar.classList.add('hidden');
                mainContent.classList.add('expanded');
                toggleIcon?.classList.add('rotate-90');
            }
        });

        // SweetAlert2 for login success
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            timer: 2500,
            showConfirmButton: false
        });
        @endif
    });
</script>

@yield('scripts')
@stack('scripts')
</body>
</html>