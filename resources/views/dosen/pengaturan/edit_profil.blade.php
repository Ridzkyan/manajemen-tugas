@extends('layouts.dosen')

@section('content')
<div class="container">
    <h4 class="mb-4">Ubah Profil Dosen</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('dosen.pengaturan.profil.update') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nama Dosen</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $dosen->name) }}" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Dosen</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $dosen->email) }}" required>
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
        <a href="{{ route('dosen.pengaturan') }}" class="btn btn-secondary ms-2">Kembali</a>
    </form>
</div>
@endsection
