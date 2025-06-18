<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Dosen')</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('css/backsite/layouts/style.css') }}" rel="stylesheet">

    <!-- Anti Flicker Sidebar Dosen -->
    <script>
    (function(){
        var key = 'dosenSidebarOpen';
        if (localStorage.getItem(key) === 'false') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    })();
    </script>
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
                <a href="{{ route('dosen.pengaturan.index') }}" class="nav-link {{ request()->is('dosen/pengaturan*') ? 'active' : '' }}">
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
            <div class="text-white text-end ms-auto d-flex align-items-center gap-2">
                <div>
                    <strong>{{ Auth::guard('dosen')->user()->name ?? 'Dosen' }}</strong><br>
                    <small>Dosen</small>
                </div>
                <img src="{{ asset(Auth::guard('dosen')->user()->foto ?? 'images/dosen/default.png') }}" 
                    alt="Foto Profil" 
                    class="rounded-circle" 
                    width="40" height="40" 
                    style="object-fit: cover; cursor: pointer;">
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
    const SIDEBAR_KEY = 'dosenSidebarOpen';

    // UTAMA: Sinkronkan class pada <html> agar sidebar bisa anti-flicker + tetap toggle
    function setSidebarStatus(status) {
        if(status === 'false') {
            sidebar.classList.add('hidden');
            mainContent.classList.add('expanded');
            toggleIcon?.classList.add('rotate-90');
            document.documentElement.classList.add('sidebar-collapsed');
        } else {
            sidebar.classList.remove('hidden');
            mainContent.classList.remove('expanded');
            toggleIcon?.classList.remove('rotate-90');
            document.documentElement.classList.remove('sidebar-collapsed');
        }
    }
    setSidebarStatus(localStorage.getItem(SIDEBAR_KEY) ?? 'true');

    toggleBtn?.addEventListener('click', function () {
        const isHidden = sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('expanded');
        toggleIcon.classList.toggle('rotate-90');
        const sidebarOpen = (!isHidden).toString();
        localStorage.setItem(SIDEBAR_KEY, sidebarOpen);
        if (sidebarOpen === 'false') {
            document.documentElement.classList.add('sidebar-collapsed');
        } else {
            document.documentElement.classList.remove('sidebar-collapsed');
        }
    });

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
            localStorage.setItem(SIDEBAR_KEY, 'false');
            document.documentElement.classList.add('sidebar-collapsed');
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('hidden');
            mainContent.classList.remove('expanded');
            toggleIcon?.classList.remove('rotate-90');
            localStorage.setItem(SIDEBAR_KEY, 'true');
            document.documentElement.classList.remove('sidebar-collapsed');
        }
    });

    // SweetAlert2 
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
