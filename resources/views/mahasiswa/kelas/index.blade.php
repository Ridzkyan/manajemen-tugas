@extends('layouts.mahasiswa')

@section('content')
<div class="container">
    <h3>Halaman Detail Kelas</h3>
    <p><strong>Nama Kelas:</strong> {{ $kelas->nama_kelas }}</p>
    <p><strong>Nama Dosen:</strong> {{ $kelas->dosen->name }}</p>

    <!-- Tombol Aksi -->
    <div class="d-flex gap-2 mt-4 flex-wrap">
        <a href="{{ route('mahasiswa.materi.index', ['kelas' => $kelas->id]) }}" class="btn btn-primary">
            📚 Lihat Materi
        </a>

        <a href="{{ route('mahasiswa.tugas.index', ['kelas' => $kelas->id]) }}" class="btn btn-success">
            📝 Lihat Tugas
        </a>

        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
            ⬅️ Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
