@extends('layouts.dosen')

@section('page_title', 'Pengaturan Dosen')

@section('content')


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link href="{{ asset('css/backsite/dosen/pengaturan.css') }}" rel="stylesheet">
{{-- Flash message SweetAlert --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
        showConfirmButton: true
    });
</script>
@endif

<div class="pengaturan-wrapper">
    <h2 class="pengaturan-title">
        <i class="bi bi-gear-fill text-warning me-2"></i> Pengaturan Dosen
    </h2>
    <p class="pengaturan-subtitle">Kelola pengaturan akun dosen di bawah ini.</p>

    <div class="row g-4 justify-content-center">

        {{-- Ubah Profil --}}
        <div class="col-md-4">
            <a href="{{ route('dosen.pengaturan.profil') }}" class="text-decoration-none">
                <div class="pengaturan-card border border-primary-subtle">
                    <div class="pengaturan-icon text-primary">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h5 class="fw-semibold text-dark mb-2 fs-5">Ubah Profil</h5>
                    <span class="badge bg-primary badge-custom">Aktif</span>
                </div>
            </a>
        </div>

        {{-- Ganti Password --}}
        <div class="col-md-4">
            <a href="{{ route('dosen.pengaturan.password.update') }}" class="text-decoration-none">
                <div class="pengaturan-card border border-danger-subtle">
                    <div class="pengaturan-icon text-danger">
                        <i class="fas fa-key"></i>
                    </div>
                    <h5 class="fw-semibold text-dark mb-2 fs-5">Ganti Password</h5>
                    <span class="badge bg-danger badge-custom">Keamanan</span>
                </div>
            </a>
        </div>

        {{-- Logout dengan Konfirmasi --}}
        <div class="col-md-4">
            <form id="logout-form" method="POST" action="{{ route('dosen.logout') }}">
                @csrf
                <button type="button" onclick="confirmLogout()" class="pengaturan-card border border-danger-subtle btn btn-link text-decoration-none w-100">
                    <div class="pengaturan-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h5 class="fw-semibold text-dark mb-2 fs-5">Logout</h5>
                    <span class="badge bg-danger badge-custom">Logout</span>
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Script Konfirmasi Logout --}}
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: "Kamu akan keluar dari akun ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

@endsection