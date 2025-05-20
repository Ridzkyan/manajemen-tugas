@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <h5 class="text-center mb-4">
        <i class="fas fa-search text-primary me-2"></i>
        Hasil pencarian untuk: <strong>{{ $q }}</strong>
    </h5>

    @if ($materi->count())
        <h6 class="fw-bold">📚 Materi</h6>
        <ul>
            @foreach ($materi as $m)
                <li>{{ $m->judul }}</li>
            @endforeach
        </ul>
    @endif

    @if ($tugas->count())
        <h6 class="fw-bold">📝 Tugas/Ujian</h6>
        <ul>
            @foreach ($tugas as $t)
                <li>{{ $t->judul }}</li>
            @endforeach
        </ul>
    @endif

    @if ($kelas->count())
        <h6 class="fw-bold">🏫 Kelas</h6>
        <ul>
            @foreach ($kelas as $k)
                <li>{{ $k->nama_matakuliah }} - {{ $k->nama_kelas }}</li>
            @endforeach
        </ul>
    @endif

    @if ($komunikasi->count())
        <h6 class="fw-bold">💬 Komunikasi</h6>
        <ul>
            @foreach ($komunikasi as $k)
                <li>Forum: {{ $k->nama_kelas }}</li>
            @endforeach
        </ul>
    @endif

    @if ($rekap->count())
        <h6 class="fw-bold">📊 Rekap Nilai</h6>
        <ul>
            @foreach ($rekap as $r)
                <li>{{ $r->nama_matakuliah }} - {{ $r->nama_kelas }}</li>
            @endforeach
        </ul>
    @endif

    @if ($pengaturan)
        <h6 class="fw-bold">⚙️ Pengaturan</h6>
        <p><a href="{{ route('dosen.pengaturan') }}">Lihat Pengaturan</a></p>
    @endif

    @if (
        !$materi->count() &&
        !$tugas->count() &&
        !$kelas->count() &&
        !$komunikasi->count() &&
        !$rekap->count() &&
        !$pengaturan
    )
        <div class="alert alert-warning text-center mt-4">
            <strong>Tidak ada hasil ditemukan.</strong>
        </div>
    @endif
</div>
@endsection