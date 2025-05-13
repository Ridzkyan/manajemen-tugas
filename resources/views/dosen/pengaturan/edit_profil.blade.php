@extends('layouts.dosen')

@section('content')
<div class="container">
    {{-- Ubah Profil Dosen --}}
    <h4 class="mb-4">Ubah Profil Dosen</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('dosen.pengaturan.profil.update') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Dosen</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $dosen->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

    <div class="mb-3">
    <label for="email" class="form-label">Email Dosen</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $dosen->email) }}" required>
    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
</div>


        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
    </form>
</div>

<hr class="my-5">

<div class="container">
    {{-- Ganti Password --}}
    <h4 class="mb-4">Ganti Password</h4>

    @if(session('success_password'))
        <div class="alert alert-success">{{ session('success_password') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('dosen.pengaturan.password.update') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control" required minlength="6">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="6">
        </div>

        <div class="d-flex justify-content-start">
            <button type="submit" class="btn btn-warning">Perbarui Password</button>
            <a href="{{ route('dosen.pengaturan') }}" class="btn btn-secondary ms-2">Kembali</a>
        </div>
    </form>
</div>
<hr class="my-4">

<form action="{{ route('dosen.logout') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-outline-danger w-100">
        <i class="fas fa-sign-out-alt me-2"></i> Logout
    </button>
</form>


@endsection
