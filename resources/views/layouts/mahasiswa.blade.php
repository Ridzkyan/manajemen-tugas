<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mahasiswa')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            overflow: hidden;
            background-color: #fef9f4;
            font-family: 'Arial', sans-serif;
        }

        #wrapper { display: flex; height: 100%; }

        .sidebar {
            background-color: #00838f;
            color: white;
            min-height: 100vh;
            width: 240px;
            transition: all 0.3s ease;
        }

        .sidebar.hide { margin-left: -240px; }

        .sidebar .logo-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
        }

        .sidebar .logo-wrapper img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .sidebar .logo-wrapper h5 {
            font-size: 1rem;
            font-weight: bold;
            color: white;
            margin-top: 10px;
            letter-spacing: 1px;
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

        .sidebar .nav-link.active {
            background-color: white;
            color: #f5a04e !important;
        }

        .main-content {
            flex: 1;
            transition: margin-left 0.3s ease;
            width: 100%;
            overflow-y: auto;
        }

        .topbar {
            background-color: #f5a04e;
            padding: 15px 30px;
            border-radius: 0 0 12px 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .search-box {
            flex: 1;
            margin: 0 30px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            border-radius: 50px;
            border: none;
            padding: 10px 20px 10px 40px;
        }

        .search-box i {
            position: absolute;
            top: 10px;
            left: 15px;
            color: #aaa;
        }

        .user-info {
            text-align: right;
            color: white;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-left: 10px;
        }

        .content-wrapper {
            padding: 30px;
        }

        .overlay { display: none; }

        #toggleSidebar {
            cursor: pointer;
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -240px;
                z-index: 1000;
            }

            .sidebar.active { left: 0; }

            .overlay.show {
                display: block;
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background-color: rgba(0,0,0,0.2);
                z-index: 999;
            }
        }
    </style>
</head>
<body>
<div id="wrapper">
    @php
        $user = Auth::guard('mahasiswa')->user();
        $foto = $user->foto ? asset($user->foto) : asset('default.png');
    @endphp

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-wrapper">
            <img src="{{ asset('images/LogoWelcome.png') }}" alt="Logo">
            <h5>TASKFLOW</h5>
        </div>
        <ul class="nav flex-column px-3">
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="{{ route('mahasiswa.kelas.index') }}">
                    <i class="fas fa-folder-open"></i> Materi & Kelas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="{{ route('mahasiswa.tugas.index') }}">
                    <i class="fas fa-file-alt"></i> Tugas & Ujian
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="{{ route('mahasiswa.komunikasi.index') }}">
                    <i class="fas fa-comments"></i> Komunikasi
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i> Rekap Nilai</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="{{ route('mahasiswa.pengaturan.index') }}">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            </li>
        </ul>
    </div>

    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center">
                <i class="fas fa-bars me-3" id="toggleSidebar"></i>
                <h5 class="mb-0">@yield('title')</h5>
            </div>
            <div class="search-box">
                <input type="text" class="form-control" placeholder="Cari...">
                <i class="fas fa-search"></i>
            </div>
            <div class="user-info me-2">
                <div><strong>{{ $user->nama }}</strong></div>
                <small>Mahasiswa</small>
            </div>
            <div class="avatar">
                <img src="{{ $foto }}" class="rounded-circle" width="40" height="40" style="object-fit: cover;" alt="Foto Profil">
            </div>
        </div>

        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
</div>

<!-- JS Sidebar toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            sidebar.classList.toggle('hide');
            toggleBtn.classList.toggle('active');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebar.classList.add('hide');
            toggleBtn.classList.remove('active');
            overlay.classList.remove('show');
        });
    });
</script>
</body>
</html>
