@extends('layouts.mahasiswa')

@section('page_title', 'Pengaturan Mahasiswa')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .pengaturan-wrapper {
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
        padding: 40px 20px 60px;
    }

    .pengaturan-title {
        font-size: 2.3rem;
        font-weight: 700;
        color: #333;
        text-align: center;
        margin-bottom: 10px;
    }

    .pengaturan-subtitle {
        text-align: center;
        color: #777;
        margin-bottom: 40px;
    }

    .pengaturan-card {
        border-radius: 20px;
        transition: 0.3s ease;
        padding: 40px 25px;
        text-align: center;
        background-color: #ffffff;
        border: 1px solid #e3e3e3;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .pengaturan-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.07);
    }

    .pengaturan-icon {
        font-size: 3rem;
        margin-bottom: 16px;
    }

    .badge-custom {
        padding: 8px 16px;
        font-size: 0.9rem;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .pengaturan-title {
            font-size: 1.8rem;
        }

        .pengaturan-card {
            padding: 30px 20px;
        }

        .pengaturan-icon {
            font-size: 2.4rem;
        }
    }
</style>

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
        <i class="bi bi-gear-fill text-warning me-2"></i> Pengaturan Mahasiswa
    </h2>
    <p class="pengaturan-subtitle">Kelola pengaturan akun Mahasiswa di bawah ini.</p>

    <div class="row g-4 justify-content-center">
        {{-- Ubah Profil --}}
        <div class="col-md-4">
            <a href="{{ route('mahasiswa.profile-edit.edit') }}" class="text-decoration-none">
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
            <a href="{{ route('mahasiswa.password-edit.edit') }}" class="text-decoration-none">
                <div class="pengaturan-card border border-danger-subtle">
                    <div class="pengaturan-icon text-danger">
                        <i class="fas fa-key"></i>
                    </div>
                    <h5 class="fw-semibold text-dark mb-2 fs-5">Ganti Password</h5>
                    <span class="badge bg-danger badge-custom">Keamanan</span>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <form id="logout-form" method="POST" action="{{ route('mahasiswa.logout') }}">
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