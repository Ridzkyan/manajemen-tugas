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
            height: 100%;
            overflow: hidden;
            background-color: #fef9f4;
            font-family: 'Arial', sans-serif;
        }

        #wrapper {
            display: flex;
            height: 100%;
        }

        .sidebar {
            background-color: #00838f;
            color: white;
            min-height: 100vh;
            width: 240px;
            transition: all 0.3s ease;
        }

        .sidebar.hide {
            margin-left: -240px;
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
</head>
<body>
<div id="wrapper">
    @php
        $user = Auth::user();
    @endphp
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
                <a class="nav-link {{ request()->routeIs('admin.dashboard.users') ? 'active' : '' }}" href="{{ route('admin.dashboard.users') }}">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-folder-open"></i> Kelas/Mata Kuliah</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-file-alt"></i> Konten</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i> Monitoring/Laporan</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="/admin/pengaturan">
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
            <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avatarModalLabel">Foto Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        @if($user->foto)
                            <img src="{{ asset($user->foto) }}" class="img-fluid rounded shadow">
                        @else
                            <img src="{{ asset('images/default.png') }}" class="img-fluid rounded shadow">
                        @endif
                    </div>
                    </div>
                </div>
            </div>
            <div class="avatar">
                <a href="#" data-bs-toggle="modal" data-bs-target="#avatarModal">
                    @if($user->foto)
                        <img src="{{ asset($user->foto) }}" class="rounded-circle" width="40" height="40" style="object-fit: cover; cursor: zoom-in;">
                    @else
                        <img src="{{ asset('images/default.png') }}" class="rounded-circle" width="40" height="40" style="object-fit: cover; cursor: zoom-in;">
                    @endif
                </a>
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
</body>
</html>