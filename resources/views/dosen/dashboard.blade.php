@extends('layouts.dosen')

@section('content')
<div class="container-fluid py-4" style="background: #FFF6ED; min-height: 100vh;">

    {{-- Welcome Box --}}
    <div class="p-4 mb-4 rounded shadow-sm" style="background-color: #F9A826; color: white;">
        <h5 class="fw-bold mb-1">Halo, {{ Auth::guard('dosen')->user()->name ?? 'Nama Dosen' }}!</h5>
        <p class="mb-0">Selamat datang, mari kita mulai hari dengan semangat dan produktivitas tinggi.</p>
    </div>

    {{-- Statistik Box --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="text-center p-4 rounded shadow-sm text-white" style="background-color: #F9A826;">
                <h3 class="fw-bold mb-1">{{ $userCount ?? 0 }}</h3>
                <p class="mb-0">Users</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="text-center p-4 rounded shadow-sm text-white" style="background-color: #F9A826;">
                <h3 class="fw-bold mb-1">{{ $mataKuliahCount ?? 0 }}</h3>
                <p class="mb-0">Mata kuliah</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="text-center p-4 rounded shadow-sm text-white" style="background-color: #F9A826;">
                <h3 class="fw-bold mb-1">{{ $materiCount ?? 0 }}</h3>
                <p class="mb-0">Konten/materi</p>
            </div>
        </div>
    </div>

    {{-- Statistik Chart dan Kelas --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm rounded h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Statistik Rata-rata Nilai</h6>
                        <a href="rekap-nilai" class="btn btn-sm text-white" style="background-color: #F9A826;">Selengkapnya</a>
                    </div>
                    {{-- Chart ID tetap sama, data akan diisi oleh controller/script --}}
                    <canvas id="nilaiChart" height="230"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm rounded h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Kelas / Mata Kuliah</h6>
                        <a href="{{ route('dosen.kelola_kelas.index', $kelas->first()->id ?? 1) }}" class="btn btn-sm text-white" style="background-color: #F9A826;">Selengkapnya</a>
                    </div>
                    @if($kelas->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($kelas as $kls)
                                <li class="mb-3 border-bottom pb-2">
                                    <strong>{{ $kls->nama_matakuliah }}</strong><br>
                                    <small class="text-muted">{{ $kls->nama_kelas }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">Belum ada kelas yang dibuat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart Script Tidak Disentuh --}}
@endsection
