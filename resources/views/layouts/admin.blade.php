<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100vh;
            overflow: hidden;
            background-color: #fef9f4;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        #wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            background-color: #00838f;
            color: white;
            width: 240px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

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

        .sidebar i {
            margin-right: 10px;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
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
            width: 100%;
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
            background-color: white;
            border-radius: 50%;
            margin-left: 10px;
        }

        .content-wrapper {
            padding: 30px;
            flex: 1;
        }

        .overlay {
            display: none;
        }

        #toggleSidebar {
            cursor: pointer;
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        #toggleSidebar.active {
            transform: rotate(90deg);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -240px;
                z-index: 1000;
            }

            .sidebar.active {
                left: 0;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.2);
                z-index: 999;
            }

            .overlay.show {
                display: block;
            }
        }
    </style>

    {{-- Halaman khusus tanpa scroll --}}
    @php
        $noScrollPages = [
            'admin/pengaturan',
            'admin/profil',
            'admin/password',
            'admin/ganti-password'
        ];
        $hideScroll = collect($noScrollPages)->contains(fn($route) => request()->is($route));
    @endphp

    @if($hideScroll)
    <style>
        html, body {
            overflow-y: hidden !important;
        }
        .main-content {
            overflow: hidden !important;
        }
        .content-wrapper {
            min-height: calc(100vh - 90px);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 !important;
        }
    </style>
    @endif
</head>
<body>
<div id="wrapper">
    @php $user = Auth::user(); @endphp

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-wrapper">
            <img src="{{ asset('images/LogoWelcome.png') }}" alt="Logo">
            <h5 class="mt-2">TASKFLOW</h5>
        </div>
        <ul class="nav flex-column px-3">
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('admin.users.index') }}"
                   class="nav-link {{ Request::is('admin/users*') ? 'active bg-white text-warning fw-bold rounded-pill px-3' : '' }}">
                    <i class="fas fa-users me-2"></i> Users
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('admin.kelas.index') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">
                    <i class="fas fa-folder-open"></i> Kelas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/konten*') ? 'active' : '' }}" href="{{ route('admin.konten.index') }}">
                    <i class="fas fa-file-alt"></i> Konten
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/monitoring*') ? 'active' : '' }}" href="{{ route('admin.monitoring') }}">
                    <i class="fas fa-chart-bar"></i> Monitoring
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->is('admin/pengaturan*') || request()->is('admin/profil*') || request()->is('admin/password*') || request()->is('admin/ganti-password*') ? 'active' : '' }}" href="{{ route('admin.pengaturan') }}">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            </li>
        </ul>
    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center">
                <i class="fas fa-bars me-3" id="toggleSidebar"></i>
                <h5 class="mb-0">Dashboard</h5>
            </div>
            <div class="search-box">
                <input type="text" class="form-control" placeholder="Cari...">
                <i class="fas fa-search"></i>
            </div>
            <div class="user-info me-2">
                <div><strong>{{ $user->name }}</strong></div>
                <small>{{ ucfirst($user->role) }}</small>
            </div>
            
            <div class="avatar">
                <a href="#" data-bs-toggle="modal" data-bs-target="#avatarModal">
                    <img src="{{ asset($user->foto ?? 'images/default.png') }}" class="rounded-circle" width="40" height="40" style="object-fit: cover; cursor: zoom-in;">
                </a>
            </div>
        </div>

        <!-- Modal Avatar -->
        <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avatarModalLabel">Foto Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset($user->foto ?? 'images/default.png') }}" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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

<!-- SweetAlert2 for Login Success -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('login_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil',
        text: '{{ session('login_success') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@stack('scripts')
</body>
</html>