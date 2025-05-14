@extends('layouts.mahasiswa')

@section('title', 'Kelas Saya')

@section('content')
<div class="container">
    <h4 class="fw-bold mb-4">Daftar Kelas yang Kamu Ikuti</h4>

    <div class="row">
        @forelse($kelasmahasiswa as $kelas)
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $kelas->nama_kelas }}</h5>
                        <p class="text-muted mb-2"><strong>Mata Kuliah:</strong> {{ $kelas->nama_matakuliah }}</p>
                        <p class="text-muted mb-2"><strong>Dosen:</strong> {{ $kelas->dosen->name ?? '-' }}</p>

                        <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="btn btn-sm btn-secondary">
                            🔍 Lihat Kelas
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Kamu belum tergabung di kelas manapun.</p>
        @endforelse
    </div>
</div>
@endsection
