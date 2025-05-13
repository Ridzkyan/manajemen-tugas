@extends('layouts.dosen')

@if(session('success'))
    <div class="alert alert-success py-2 px-3 mb-3 small alert-dismissible fade show" role="alert" style="font-size: 0.9rem;">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger py-2 px-3 mb-3 small alert-dismissible fade show" role="alert" style="font-size: 0.9rem;">
        {{ session('error') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@section('content')
<div class="container">
    <h3 class="mb-4 fw-bold">⚙️ Pengaturan Dosen</h3>
    <p class="text-muted mb-4">Kelola pengaturan akun dosen di bawah ini.</p>

    <div class="row g-4">

        {{-- Ubah Profile --}}
        <div class="col-md-4">
            <a href="{{ route('dosen.pengaturan.profil') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center p-4 border border-primary-subtle">
                    <div class="text-primary mb-2">
                        <i class="fas fa-user-cog fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Ubah Profil</h5>
                    <span class="badge bg-primary mt-2">Aktif</span>
                </div>
            </a>
        </div>

        {{-- Ganti Password --}}
        <div class="col-md-4">
            <a href="{{ route('dosen.pengaturan.password.update') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 text-center p-4 border border-danger-subtle">
                    <div class="text-danger mb-2">
                        <i class="fas fa-key fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Ganti Password</h5>
                    <span class="badge bg-danger mt-2">Keamanan</span>
                </div>
            </a>
        </div>

        {{-- Logout --}}
        <div class="col-md-4">
            <form method="POST" action="{{ route('dosen.logout') }}">
                @csrf
                <button type="submit" class="card shadow-sm h-100 text-center p-4 w-100 border border-danger-subtle btn btn-link text-decoration-none">
                    <div class="text-danger mb-2">
                        <i class="fas fa-sign-out-alt fa-2x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Logout</h5>
                    <span class="badge bg-danger mt-2">Logout</span>
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
