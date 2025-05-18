@extends('layouts.mahasiswa')

@section('title', 'Materi Kelas')

@section('content')
<div class="container">
    <h3 class="mb-4">Materi Kelas: {{ $kelas->nama_kelas ?? $kelas->nama_matakuliah ?? 'Nama Kelas' }}</h3>

    @if($materis->count() > 0)
        <ul class="list-group">
            @foreach($materis as $materi)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $materi->judul }}</span>
                    @if($materi->tipe == 'pdf')
                        <a href="{{ asset('storage/' . $materi->file) }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener noreferrer">📄 Lihat PDF</a>
                    @elseif($materi->tipe == 'video' || $materi->link)
                        <a href="{{ $materi->link }}" class="btn btn-sm btn-outline-info" target="_blank" rel="noopener noreferrer">▶️ Tonton Video</a>
                    @else
                        <span class="text-muted small">Tidak ada file atau link tersedia</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-muted mt-3">Belum ada materi di kelas ini.</p>
    @endif

    <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="btn btn-secondary mt-3">⬅️ Kembali</a>
</div>
@endsection
