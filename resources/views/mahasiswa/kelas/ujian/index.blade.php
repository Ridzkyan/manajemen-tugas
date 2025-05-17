@extends('layouts.mahasiswa')
@section('title', 'Daftar Ujian')

@section('content')
<div class="container">
    <h4 class="mb-4">Daftar Ujian - Kelas: {{ $kelas->nama_matakuliah }}</h4>

    @if(isset($ujians) && count($ujians) > 0)
        @foreach($ujians as $ujian)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ $ujian->judul }}</h5>
                    <p class="mb-1"><strong>Deskripsi:</strong> {{ $ujian->deskripsi }}</p>
                    <p class="mb-1"><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($ujian->deadline)->translatedFormat('d F Y - H:i') }}</p>

                    @if(in_array($ujian->id, $ujianDikerjakan ?? []))
                        <span class="badge bg-success">✅ Sudah Dikerjakan</span>
                        <a href="{{ route('mahasiswa.ujian.preview', [$kelasId, $ujian->id]) }}" class="btn btn-sm btn-outline-info ms-2">Lihat Jawaban</a>
                    @else
                        <a href="{{ route('mahasiswa.ujian.kerjakan', [$kelasId, $ujian->id]) }}" class="btn btn-sm btn-warning">Kerjakan</a>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">Belum ada ujian pada kelas ini.</div>
    @endif
</div>
@endsection
