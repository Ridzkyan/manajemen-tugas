@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <!-- Bagian Kiri: Gambar -->
        <div class="col-md-6 d-none d-md-block" style="background: url('{{ asset("images/Manusia.jpg") }}') center/cover no-repeat;"></div>

        <!-- Bagian Kanan: Form Register -->
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-light">
            <div class="w-75">
                <!-- Link ke Halaman Login -->
                <div class="text-end mb-4">
                    <small>Sudah punya akun?</small>
                    <a href="{{ route('login') }}" class="btn btn-outline-custom">Log In</a>
                </div>

                <!-- Judul -->
                <h2 class="fw-bold mb-2">Buat Akun TaskFlow</h2>
                <p class="text-muted mb-4">Daftar sekarang untuk mulai menggunakan TaskFlow</p>

                <!-- Form Register -->
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Nama --}}
                    <div class="form-group mb-3">
                        <label for="name">Nama Lengkap</label>
                        <input id="name" type="text" name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" 
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-group mb-4">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" 
                               class="form-control" required>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary-custom">Sign Up</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection