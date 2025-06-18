<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard Mahasiswa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/backsite/layouts/style.css') }}" rel="stylesheet">

   
    <script>
    (function(){
        var key = 'mahasiswaSidebarOpen';
        if (localStorage.getItem(key) === 'false') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    })();
    </script>
</head>
<body>
<div id="app">
    @php
        $user = Auth::guard('mahasiswa')->user();
        $foto = $user->foto ? asset($user->foto) : asset('images/default.png');
        $kelasAktifId = $kelasId ?? optional($user->kelasMahasiswa()->first())->id;
    @endphp

    <aside class="sidebar" id="sidebar">
        <h5>TASKFLOW</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link {{ Route::is('mahasiswa.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-link {{ request()->is('mahasiswa/kelas') || request()->is('mahasiswa/kelas/*') && !request()->is('mahasiswa/kelas/*/tugas*') ? 'active' : '' }}">
                    <i class="fas fa-folder-open"></i> Materi & Kelas
                </a>
            </li>
            <li class="nav-item mb-2">
                @if($kelasAktifId)
                    <a href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $kelasAktifId]) }}" class="nav-link {{ request()->is("mahasiswa/kelas/$kelasAktifId/tugas*") ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Tugas & Ujian
                    </a>
                @else
                    <a class="nav-link disabled text-white-50" href="#">
                        <i class="fas fa-file-alt"></i> Tugas & Ujian
                    </a>
                @endif
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.join.index') }}" class="nav-link {{ Route::is('mahasiswa.join.index') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i> Gabung Kelas
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.komunikasi.index') }}" class="nav-link {{ Route::is('mahasiswa.komunikasi.index') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i> Komunikasi
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('mahasiswa.pengaturan.index') }}" class="nav-link {{ request()->is('mahasiswa/pengaturan*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            </li>
        </ul>
    </aside>

    <div class="main-content" id="mainContent">
        <div class="topbar">
            <button id="toggleSidebar" class="btn border-0 bg-transparent p-0 me-3">
                <i id="toggleIcon" class="fas fa-bars fa-lg transition-rotate"></i>
            </button>
            <h5 class="mb-0 me-3">Dashboard Mahasiswa</h5>
            <div class="text-white text-end ms-auto d-flex align-items-center gap-2">
                <div>
                    <strong>{{ $user->name }}</strong><br>
                    <small>Mahasiswa</small>
                </div>
                <a href="#" data-bs-toggle="modal" data-bs-target="#avatarModal">
                    <img src="{{ $foto }}" onerror="this.onerror=null;this.src='{{ asset('images/default.png') }}';" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                </a>
            </div>
        </div>

        <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="avatarModalLabel">Foto Profil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ $foto }}" class="img-fluid rounded shadow" />
                    </div>
                </div>
            </div>
        </div>

        <div class="content-wrapper container-fluid py-4">
            @if (auth()->guard('mahasiswa')->check() && !auth()->guard('mahasiswa')->user()->hasVerifiedEmail())
                <div class="alert alert-warning text-center mb-4" role="alert">
                    <strong>Email kamu belum terverifikasi.</strong> Silakan cek inbox atau klik <a href="{{ route('mahasiswa.email-verification.notice') }}" class="alert-link">verifikasi ulang</a>.
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebar');
    const toggleIcon = document.getElementById('toggleIcon');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const SIDEBAR_KEY = 'mahasiswaSidebarOpen';

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
});
</script>
@stack('scripts')
</body>
</html>
