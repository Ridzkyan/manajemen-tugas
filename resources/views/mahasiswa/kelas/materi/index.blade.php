@extends('layouts.mahasiswa')

@section('title', 'Materi Kelas')

@section('content')
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/materi_kelas.css') }}">

<div class="materi-wrapper">
    <div class="materi-title">
        <i class="bi bi-journal-bookmark-fill icon-buku me-2"></i>
        Materi {{ $kelas->nama_kelas ?? $kelas->nama_matakuliah ?? 'Nama Kelas' }}
    </div>

    @if($materis->count() > 0)
        @foreach($materis as $materi)
            <div class="materi-card">
                <div class="materi-judul">{{ $materi->judul }}</div>
                <div>
                    @if($materi->tipe == 'pdf')
                        <a href="{{ asset('storage/' . $materi->file) }}" class="btn btn-outline-primary btn-materi" target="_blank">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Lihat PDF
                        </a>
                    @elseif($materi->tipe == 'video' || $materi->link)
                        <a href="{{ $materi->link }}" class="btn btn-outline-info btn-materi" target="_blank">
                            <i class="bi bi-play-btn-fill me-1 icon-video"></i> Video
                        </a>
                    @else
                        <span class="text-muted small">
                            <i class="bi bi-x-octagon-fill text-danger me-1"></i> Tidak tersedia
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="no-materi">Belum ada materi di kelas ini.</div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Kelas
        </a>
    </div>
</div>
@endsection
