@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Pilih Kelas untuk Mengelola Tugas & Ujian</h4>

    <div class="row">
        @foreach($kelasList as $kelas)
        <div class="col-md-4 mb-4">
            <a href="{{ route('dosen.tugas_ujian.index', $kelas->id) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $kelas->nama_kelas }}</h5>
                        <p class="mb-0 text-muted">{{ $kelas->nama_matakuliah }}</p>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
