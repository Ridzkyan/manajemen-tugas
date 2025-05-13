@extends('layouts.mahasiswa')

@section('content')
<div class="container">
    <h3>Materi Kelas: {{ $kelas->nama_kelas }}</h3>

    @if($materis->count() > 0)
        <ul class="list-group">
            @foreach($materis as $materi)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $materi->judul }}</span>
                    @if($materi->tipe == 'pdf')
                        <a href="{{ asset('storage/' . $materi->file) }}" class="btn btn-sm btn-outline-primary" target="_blank">📄 Lihat PDF</a>
                    @else
                        <a href="{{ $materi->link }}" class="btn btn-sm btn-outline-info" target="_blank">▶️ Tonton Video</a>
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
