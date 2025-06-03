<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    
    <link href="{{ asset('css/backsite/layouts/style.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="text-center">
            <h5 class="mt-2">TASKFLOW</h5>
        </div>
        <ul class="nav flex-column px-2 mt-3">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-th-large me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.kelas.index') ? 'active' : '' }}" href="{{ route('admin.kelas.index') }}">
                    <i class="fas fa-folder-open me-2"></i> Kelas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/konten*') ? 'active' : '' }}" href="{{ route('admin.konten.index') }}">
                    <i class="fas fa-file-alt me-2"></i> Konten
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/monitoring*') ? 'active' : '' }}" href="{{ route('admin.monitoring') }}">
                    <i class="fas fa-chart-bar me-2"></i> Monitoring
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/pengaturan*') ? 'active' : '' }}" href="{{ route('admin.pengaturan') }}">
                    <i class="fas fa-cog me-2"></i> Pengaturan
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Topbar -->
        <div class="topbar">
            <button id="toggleSidebar" class="btn border-0 bg-transparent p-0 me-3">
                <i id="toggleIcon" class="fas fa-bars fa-lg text-white"></i>
            </button>
            <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            <div class="user-info d-flex align-items-center text-white">
                <div class="text-end me-2">
                    <strong>{{ Auth::user()->name }}</strong><br>
                    <small>Admin</small>
                </div>
                <img src="{{ asset(Auth::user()->foto ?? 'images/default.png') }}" alt="Foto Profil" onerror="this.src='{{ asset('images/default.png') }}'">
            </div>
        </div>

        <!-- Konten -->
        <div class="content-wrapper p-4">
            @yield('content')
        </div>
    </div>
</div>

<!-- Script -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleSidebar');
        const toggleIcon = document.getElementById('toggleIcon');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        toggleBtn?.addEventListener('click', function () {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
            toggleIcon?.classList.toggle('rotate-90');
        });

        document.addEventListener('click', function (e) {
            const isMobile = window.innerWidth <= 768;
            if (
                isMobile &&
                sidebar &&
                !sidebar.contains(e.target) &&
                !toggleBtn.contains(e.target) &&
                !sidebar.classList.contains('hidden')
            ) {
                sidebar.classList.add('hidden');
                mainContent.classList.add('expanded');
                toggleIcon?.classList.add('rotate-90');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
