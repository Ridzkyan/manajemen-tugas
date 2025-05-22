@extends('layouts.mahasiswa')

@section('title', 'Detail Kelas')

@section('content')
<style>
    .detail-kelas-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .detail-header {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 25px;
        text-align: center;
        color: #333;
    }

    .card-detail {
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        background-color: #fff;
        padding: 30px;
    }

    .card-detail h5 {
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 20px;
        color: #008080;
    }

    .info-item {
        margin-bottom: 12px;
        font-size: 1rem;
    }

    .info-item strong {
        width: 140px;
        display: inline-block;
        color: #555;
    }

    .btn-action {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
    }

    .btn-primary {
        background-color: #008080;
        border: none;
    }

    .btn-primary:hover {
        background-color: #006666;
    }

    .btn-secondary {
        background-color: #e0e0e0;
        color: #333;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #c6c6c6;
    }
</style>

<div class="detail-kelas-wrapper">
    <div class="detail-header">📘 Detail Kelas</div>

    <div class="card-detail">
        <h5>{{ $kelas->nama_kelas }}</h5>

        <div class="info-item"><strong>Mata Kuliah:</strong> {{ $kelas->nama_matakuliah }}</div>
        <div class="info-item"><strong>Dosen Pengampu:</strong> {{ $kelas->dosen->name ?? '-' }}</div>
        <div class="info-item"><strong>Kode Unik:</strong> {{ $kelas->kode_unik }}</div>

        <div class="mt-4 d-flex flex-wrap gap-2">
            <a href="{{ route('mahasiswa.materi.index', ['kelas' => $kelas->id]) }}" class="btn btn-primary btn-action">
                📚 Lihat Materi
            </a>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="btn btn-secondary btn-action">
                ⬅️ Kembali
            </a>
        </div>
    </div>
</div>
@endsection
