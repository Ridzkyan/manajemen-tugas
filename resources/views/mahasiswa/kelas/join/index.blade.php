@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">🔑 Gabung ke Kelas</h3>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form Input Kode Unik --}}
    <form method="POST" action="{{ route('mahasiswa.join.store') }}">
        @csrf

        <div class="mb-3">
            <label for="kode_unik" class="form-label">Masukkan Kode Unik Kelas</label>
            <input type="text" id="kode_unik" name="kode_unik" class="form-control" placeholder="Contoh: ABC123" required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Gabung Kelas</button>
            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">⬅️ Kembali ke Dashboard</a>
        </div>
    </form>
</div>
@endsection
