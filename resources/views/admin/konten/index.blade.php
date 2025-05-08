@extends('layouts.admin')

@section('title', 'Konten Terbaru')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Konten Terbaru</h4>

    {{-- === Materi Terbaru === --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            Materi Terbaru
        </div>
        <div class="card-body">
            @forelse($materiTerbaru as $materi)
                <div class="mb-3">
                    <strong>{{ $materi->judul }}</strong><br>
                    {{ $materi->kelas->nama_matakuliah ?? '-' }} - {{ $materi->kelas->kode_unik ?? '-' }} <br>
                    @if($materi->file)
                        <a href="{{ asset('storage/' . $materi->file) }}" target="_blank">Download Materi</a>
                    @endif
                </div>
            @empty
                <p class="text-muted">Belum ada materi terbaru.</p>
            @endforelse
        </div>
    </div>

    {{-- === Tugas Terbaru === --}}
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            Tugas Terbaru
        </div>
        <div class="card-body">
            @forelse($tugasTerbaru as $tgs)
                <div class="mb-3">
                    <strong>{{ $tgs->judul }}</strong><br>
                    {{ $tgs->kelas->nama_matakuliah ?? '-' }} - {{ $tgs->kelas->kode_unik ?? '-' }} <br>
                    @if($tgs->file_soal)
                        <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank">Download File Tugas</a>
                    @endif
                </div>
            @empty
                <p class="text-muted">Belum ada tugas terbaru.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
