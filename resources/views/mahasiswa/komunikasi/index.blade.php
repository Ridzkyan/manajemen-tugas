@extends('layouts.mahasiswa')

@section('title', 'Komunikasi')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold">Komunikasi Kelas</h4>

    <div class="row">
        @forelse($kelasmahasiswa as $kelas)
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $kelas->nama_kelas }}</h5>
                        <p class="text-muted mb-2"><strong>Mata Kuliah:</strong> {{ $kelas->nama_matakuliah }}</p>

                        @if($kelas->whatsapp_link)
                            <a href="{{ $kelas->whatsapp_link }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-whatsapp"></i> Masuk Grup WA
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>Tidak ada link WA</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center">Kamu belum tergabung di kelas manapun.</p>
        @endforelse
    </div>
</div>
@endsection