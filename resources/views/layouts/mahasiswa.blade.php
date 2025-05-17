<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mahasiswa')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #fef9f4;
            font-family: 'Arial', sans-serif;
        }

        #wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #008080;
            color: white;
            width: 240px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar .nav-link {
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 30px;
            margin: 5px 10px;
            display: flex;
            align-items: center;
        }

        .sidebar .nav-link.active {
            background-color: white;
            color: #f5a04e !important;
        }

        .sidebar .nav-link.disabled {
            pointer-events: none;
            opacity: 0.6;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .topbar {
            background-color: #f5a04e;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar .search-box input {
            border: none;
            border-radius: 20px;
            padding: 6px 15px;
            width: 250px;
        }

        .topbar .profile-info {
            display: flex;
            align-items: center;
            color: white;
        }

        .topbar .profile-info img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            object-fit: cover;
            margin-left: 15px;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div id="wrapper">
       @php
    $user = Auth::guard('mahasiswa')->user();
    $foto = $user->foto ? asset($user->foto) : asset('default.png');
    // Cek fallback jika $kelasId tidak tersedia
    $kelasAktifId = $kelasId ?? optional($user->kelasMahasiswa()->first())->id;
@endphp


        <!-- Sidebar -->
        <div class="sidebar">
            <div class="text-center py-4">
                <img src="{{ asset('images/LogoWelcome.png') }}" width="60" alt="Logo">
                <h5 class="mt-2">TASKFLOW</h5>
            </div>
            <ul class="nav flex-column mt-2">
                <li>
                    <a class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('mahasiswa.kelas.index') ? 'active' : '' }}" href="{{ route('mahasiswa.kelas.index') }}">
                        <i class="fas fa-folder-open"></i> Materi & Kelas
                    </a>
                </li>

             {{-- Tugas --}}
                <li>
                    @if($kelasAktifId)
                        <a class="nav-link {{ request()->is("mahasiswa/kelas/$kelasAktifId/tugas*") ? 'active' : '' }}"
                        href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $kelasAktifId]) }}">
                            <i class="fas fa-file-alt"></i> Tugas
                        </a>
                    @else
                        <a class="nav-link text-white-50 disabled" href="#">
                            <i class="fas fa-file-alt"></i> Tugas
                        </a>
                    @endif
                </li>

                {{-- Ujian --}}
                <li>
                    @if($kelasAktifId)
                        <a class="nav-link {{ request()->is("mahasiswa/kelas/$kelasAktifId/ujian*") ? 'active' : '' }}"
                        href="{{ route('mahasiswa.ujian.index', ['kelas' => $kelasAktifId]) }}">
                            <i class="fas fa-file-signature"></i> Ujian
                        </a>
                    @else
                        <a class="nav-link text-white-50 disabled" href="#">
                            <i class="fas fa-file-signature"></i> Ujian
                        </a>
                    @endif
                </li>


                {{-- Gabung Kelas --}}
                <li>
                    <a class="nav-link {{ request()->routeIs('mahasiswa.join.index') ? 'active' : '' }}" href="{{ route('mahasiswa.join.index') }}">
                        <i class="fas fa-plus-circle"></i> Gabung Kelas
                    </a>
                </li>

                {{-- Komunikasi --}}
                <li>
                    <a class="nav-link {{ request()->routeIs('mahasiswa.komunikasi.index') ? 'active' : '' }}" href="{{ route('mahasiswa.komunikasi.index') }}">
                        <i class="fas fa-comments"></i> Komunikasi
                    </a>
                </li>

                {{-- Pengaturan --}}
                <li>
                    <a class="nav-link {{ request()->routeIs('mahasiswa.pengaturan.index') ? 'active' : '' }}" href="{{ route('mahasiswa.pengaturan.index') }}">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-md-none"><i class="fas fa-bars"></i></button>
                    <h5 class="mb-0 text-white">@yield('title')</h5>
                </div>

                <div class="d-flex align-items-center">
                    <div class="search-box me-4">
                        <input type="text" class="form-control" placeholder="Cari...">
                    </div>
                    <div class="profile-info">
                        <span>{{ $user->nama }}</span>
                        <img src="{{ $foto }}" alt="Foto Profil">
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
