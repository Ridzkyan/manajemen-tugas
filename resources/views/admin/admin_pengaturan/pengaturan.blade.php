@extends('layouts.admin')

@section('title', 'Pengaturan Admin')

@section('content')

<!-- Link ke file CSS eksternal -->
<link rel="stylesheet" href="{{ asset('css/backsite/admin/pengaturan.css') }}">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="setting-wrapper py-5">
    <div class="setting-title">
        <i class="fas fa-cogs"></i> Pengaturan Admin
    </div>
    <p class="setting-subtext">Kelola pengaturan akun admin dan sistem di bawah ini.</p>

    <div class="setting-grid-row">
        <a href="{{ route('admin.profil.edit') }}" class="setting-card text-decoration-none text-dark">
            <i class="fas fa-user-cog text-primary"></i>
            <div class="badge bg-primary text-white">Aktif</div>
            <div class="fw-bold mt-2">Ubah Profile</div>
        </a>
        <a href="{{ route('admin.password.edit') }}" class="setting-card text-decoration-none text-dark">
            <i class="fas fa-key text-danger"></i>
            <div class="badge bg-danger text-white">Keamanan</div>
            <div class="fw-bold mt-2">Ganti Password</div>
        </a>
    </div>

    <div class="setting-grid-row">
        <div class="setting-card">
            <i class="fas fa-bell text-info"></i>
            <div class="badge bg-secondary text-white">Soon</div>
            <div class="fw-bold mt-2">Notifikasi</div>
        </div>
        <a href="{{ route('admin.pengaturan.data') }}" class="setting-card text-decoration-none text-dark">
            <i class="fas fa-sliders-h text-success"></i>
            <div class="badge bg-success text-white">Aktif</div>
            <div class="fw-bold mt-2">Backup & Restore Data</div>
        </a>
        <div class="setting-card text-danger" onclick="handleLogout()" style="cursor: pointer;">
            <i class="fas fa-sign-out-alt"></i>
            <div class="badge bg-danger text-white">Logout</div>
            <div class="fw-bold mt-2">Logout</div>
            <form id="logoutForm" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>

<script>
    function handleLogout() {
        Swal.fire({
            title: 'Yakin ingin melakukan logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            animation: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });
</script>
@endif

@endsection
