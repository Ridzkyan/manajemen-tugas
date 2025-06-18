@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <h5 class="text-center mb-4">
        <i class="bi bi-search text-primary me-2"></i>
        Hasil pencarian untuk: <strong>{{ $q }}</strong>
    </h5>

    {{-- Materi --}}
    @if ($materi->count())
        <h6 class="fw-bold mb-2">
            <i class="bi bi-journal-bookmark-fill me-1"></i> Materi 
            <span class="badge bg-primary">{{ $materi->count() }}</span>
        </h6>
        <ul>
            @foreach ($materi as $m)
                <li>
                    <a href="{{ route('dosen.materi_kelas.detail', ['id' => $m->id, 'slug' => \Str::slug($m->judul)]) }}">
                        {{ $m->judul }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Tugas/Ujian --}}
    @if ($tugas->count())
        <h6 class="fw-bold mb-2">
            <i class="bi bi-pencil-square me-1"></i> Tugas/Ujian 
            <span class="badge bg-primary">{{ $tugas->count() }}</span>
        </h6>
        <ul>
            @foreach ($tugas as $t)
                <li>
                    <a href="{{ route('dosen.tugas_ujian.detail', ['kelas' => $t->kelas_id, 'tugas' => $t->id]) }}">
                        {{ $t->judul }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Kelas --}}
    @if ($kelas->count())
        <h6 class="fw-bold mb-2">
            <i class="bi bi-building me-1"></i> Kelas 
            <span class="badge bg-primary">{{ $kelas->count() }}</span>
        </h6>
        <ul>
            @foreach ($kelas as $k)
                <li>
                    <a href="{{ route('dosen.kelola_kelas.show', $k->id) }}">
                        {{ $k->nama_matakuliah }} - {{ $k->nama_kelas }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Komunikasi --}}
    @if ($komunikasi->count())
        <h6 class="fw-bold mb-2">
            <i class="bi bi-chat-dots-fill me-1"></i> Komunikasi 
            <span class="badge bg-primary">{{ $komunikasi->count() }}</span>
        </h6>
        <ul>
            @foreach ($komunikasi as $k)
                <li>
                    Forum: 
                    <a href="{{ route('dosen.komunikasi', ['kelas' => $k->id]) }}">
                        {{ $k->nama_kelas }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Rekap Nilai --}}
    @if ($rekap->count())
        <h6 class="fw-bold mb-2">
            <i class="bi bi-bar-chart-fill me-1"></i> Rekap Nilai 
            <span class="badge bg-primary">{{ $rekap->count() }}</span>
        </h6>
        <ul>
            @foreach ($rekap as $r)
                <li>
                    <a href="{{ route('dosen.rekap_nilai.detail', ['kelas' => $r->id]) }}">
                        {{ $r->nama_matakuliah }} - {{ $r->nama_kelas }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Pengaturan --}}
    @if ($pengaturan)
        <h6 class="fw-bold">
            <i class="bi bi-gear-fill me-1"></i> Pengaturan
        </h6>
        <p>
            <a href="{{ route('dosen.pengaturan') }}">Lihat Pengaturan</a>
        </p>
    @endif

    {{-- Jika Tidak Ada Hasil --}}
    @if (
        !$materi->count() &&
        !$tugas->count() &&
        !$kelas->count() &&
        !$komunikasi->count() &&
        !$rekap->count() &&
        !$pengaturan
    )
        <div class="alert alert-warning text-center mt-4">
            <strong>
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Tidak ada hasil ditemukan.
            </strong>
        </div>
    @endif
</div>
@endsection
