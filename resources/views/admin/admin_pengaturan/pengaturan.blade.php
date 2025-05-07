@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold">⚙️ Pengaturan Admin</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <p>Selamat datang di halaman pengaturan admin.</p>

            <ul>
                <li><a href="{{ route('admin.profil.edit') }}"><i class="fas fa-user"></i> Ubah Profil</a></li>
                <li><a href="{{ route('admin.password.edit') }}"><i class="fas fa-lock"></i> Ganti Password</a></li>
                <li>Pengaturan Notifikasi</li>
                <li>Pengaturan Sistem</li>
            </ul>

            <hr>

            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
