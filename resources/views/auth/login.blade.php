@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        <!-- Gambar Kiri -->
        <div class="col-md-6 d-none d-md-block" style="background: url('{{ asset("images/Manusia.jpg") }}') center/cover no-repeat;"></div>

        <!-- Form Login -->
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-light">
            <div class="w-75">

                {{-- Link ke register --}}
                <div class="text-end mb-4">
                    <small>Belum punya akun?</small>
                    <a href="{{ route('register') }}" class="btn btn-outline-custom">Sign Up</a>
                </div>

                {{-- Judul --}}
                <h2 class="fw-bold mb-2">Selamat Datang di TaskFlow</h2>
                <p class="text-muted mb-4">Login Akun</p>

                {{-- Alert --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ url('/login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="form-group mb-3">
                        <label for="email">Alamat Email</label>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group mb-3">
                        <label for="password">Kata Sandi</label>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="form-group mb-3">
                        <label for="role">Login Sebagai</label>
                        <select name="role" id="role" class="form-control" required onchange="toggleKodeUnik()">
                            <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        </select>
                    </div>

                    {{-- Kode Unik untuk Dosen --}}
                    <div class="form-group mb-4" id="kodeUnikField" style="display: none;">
                        <label for="kode_unik">Kode Unik Dosen</label>
                        <input id="kode_unik" type="text"
                               class="form-control @error('kode_unik') is-invalid @enderror"
                               name="kode_unik" value="{{ old('kode_unik') }}">
                        @error('kode_unik')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tombol Login --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary-custom">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Toggle Kode Unik --}}
<script>
function toggleKodeUnik() {
    const role = document.getElementById('role').value;
    const kodeUnikField = document.getElementById('kodeUnikField');
    kodeUnikField.style.display = (role === 'dosen') ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', toggleKodeUnik);
</script>
@endsection
