@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Manajemen Kelas: {{ $kelas->nama_kelas }}</h3>

    <div class="row">
        <div class="col-md-6 mb-3">
            <a href="{{ route('dosen.materi.index', $kelas->id) }}" class="btn btn-primary w-100">Kelola Materi</a>
        </div>
        <div class="col-md-6 mb-3">
            <a href="{{ route('dosen.tugas.index', $kelas->id) }}" class="btn btn-success w-100">Kelola Tugas/Ujian</a>
        </div>
    </div>

    <a href="{{ route('dosen.kelas.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Kelas</a>
</div>
@endsection
