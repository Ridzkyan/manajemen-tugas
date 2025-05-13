@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">{{ $kelas->nama_matakuliah }} ({{ $kelas->nama_kelas }})</h4>
        <a href="{{ route('dosen.kelas.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="mb-4">
        <a href="{{ route('dosen.tugas.index', $kelas->id) }}" class="btn btn-warning text-white">+ Tambah tugas</a>
    </div>

    @forelse($tugas as $item)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $item->judul }} ({{ ucfirst($item->tipe) }})</h5>
                <p class="card-text">
                    @if($item->file_soal)
                        <i class="bi bi-file-earmark-pdf"></i>
                        <a href="{{ asset('storage/' . $item->file_soal) }}" target="_blank">{{ basename($item->file_soal) }}</a>
                    @else
                        Tidak ada file
                    @endif
                </p>
                <a href="{{ route('dosen.tugas.penilaian', [$kelas->id, $item->id]) }}" class="btn btn-sm btn-outline-primary">
                    Selengkapnya
                </a>
            </div>
        </div>
    @empty
        <p class="text-muted">Belum ada tugas atau ujian di kelas ini.</p>
    @endforelse
</div>
@endsection
