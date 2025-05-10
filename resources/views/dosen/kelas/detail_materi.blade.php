@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold">{{ $kelas->nama_kelas }} - {{ $kelas->nama_matakuliah }}</h4>

    <h5 class="mb-3">Daftar Materi</h5>

    @if($materis->count())
        @foreach($materis as $materi)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ $materi->judul }}</h5>
                    @if($materi->tipe === 'pdf')
                        <p><a href="{{ asset('storage/' . $materi->file) }}" target="_blank">📄 Lihat PDF</a></p>
                    @elseif($materi->tipe === 'link')
                        <p><a href="{{ $materi->link }}" target="_blank">▶️ Tonton Video</a></p>
                        <img src="https://img.youtube.com/vi/{{ substr($materi->link, strpos($materi->link, 'v=') + 2, 11) }}/0.jpg" width="200">
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <p class="text-muted">Belum ada materi yang diunggah untuk kelas ini.</p>
    @endif

    <a href="{{ route('dosen.materikelas') }}" class="btn btn-secondary mt-3">⬅️ Kembali</a>
</div>
@endsection
