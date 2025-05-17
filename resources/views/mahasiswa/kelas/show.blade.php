@extends('layouts.mahasiswa')

@section('title', 'Detail Kelas')

@section('content')
<div class="container">
    <h4 class="fw-bold mb-4">Detail Kelas</h4>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">{{ $kelas->nama_kelas }}</h5>
            <p class="text-muted mb-1"><strong>Mata Kuliah:</strong> {{ $kelas->nama_matakuliah }}</p>
            <p class="text-muted mb-1"><strong>Dosen Pengampu:</strong> {{ $kelas->dosen->name ?? '-' }}</p>
            <p class="text-muted mb-1"><strong>Kode Unik:</strong> {{ $kelas->kode_unik }}</p>

            <div class="mt-4 d-flex gap-2 flex-wrap">
                <a href="{{ route('mahasiswa.materi.index', ['kelas' => $kelas->id]) }}" class="btn btn-primary">
                    📚 Lihat Materi
                </a>

                <a href="{{ route('mahasiswa.kelas.index') }}" class="btn btn-secondary">
                    ⬅️ Kembali ke Daftar Kelas
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
