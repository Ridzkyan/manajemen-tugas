@extends('layouts.mahasiswa')

@section('title', 'Detail Kelas')

@section('content')

{{-- Import CSS khusus halaman detail kelas --}}
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/kelas_detail.css') }}">

<div class="detail-kelas-wrapper">
    <div class="detail-header">
        <i class="bi bi-journal-richtext icon-f5a04e me-2"></i>
        Detail Kelas
    </div>

    <div class="card-detail">
        <h5>{{ $kelas->nama_kelas }}</h5>

        <div class="info-item"><strong>Mata Kuliah:</strong> {{ $kelas->nama_matakuliah }}</div>
        <div class="info-item"><strong>Dosen Pengampu:</strong> {{ $kelas->dosen->name ?? '-' }}</div>
        <div class="info-item"><strong>Kode Unik:</strong> {{ $kelas->kode_unik }}</div>

        <div class="mt-4 d-flex flex-wrap gap-2">
            <a href="{{ route('mahasiswa.materi.index', ['kelas' => $kelas->id]) }}" class="btn btn-primary btn-action">
                <i class="bi bi-journal-bookmark-fill me-1"></i> Lihat Materi
            </a>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="btn btn-secondary btn-action">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
