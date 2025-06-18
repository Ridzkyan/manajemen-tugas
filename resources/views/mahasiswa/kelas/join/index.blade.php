@extends('layouts.mahasiswa')

@section('page_title', 'Pengaturan Mahasiswa')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/join_kelas.css') }}">


{{-- SweetAlert Flash message khusus kelas --}}
@if(session('kelas_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('kelas_success') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

@if(session('kelas_error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('kelas_error') }}',
        showConfirmButton: true
    });
</script>
@endif

<div class="pengaturan-wrapper">
    <h2 class="pengaturan-title">
        <i class="bi bi-door-open-fill text-warning me-2"></i>
        Gabung ke Kelas
    </h2>

   
    @if(session('kelas_success'))
        <div class="alert alert-success">{{ session('kelas_success') }}</div>
    @endif
    @if(session('kelas_error'))
        <div class="alert alert-danger">{{ session('kelas_error') }}</div>
    @endif



    {{-- Form Input Kode Unik --}}
    <form method="POST" action="{{ route('mahasiswa.join.store') }}">
        @csrf
        <div class="mb-3">
            <label for="kode_unik" class="form-label">Masukkan Kode Unik Kelas</label>
            <input type="text" id="kode_unik" name="kode_unik" class="form-control" placeholder="KLS- " required>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                Gabung Kelas
            </button>
            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </form>
</div>
@endsection
