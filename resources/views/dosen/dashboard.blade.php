@extends('layouts.dosen')

@section('content')
<div class="container-fluid py-4" style="background: #FFF6ED; min-height: 100vh;">

    {{-- Welcome Alert --}}
    @if (session('success'))
    <div id="welcomeAlert" class="alert d-flex justify-content-between align-items-center mb-4 rounded shadow-sm px-4 py-3"
         style="background-color: #008080; color: white;">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-3"></i>
            <div>
                <h6 class="fw-bold mb-1">Halo, {{ Auth::guard('dosen')->user()->name ?? 'Nama Dosen' }}!</h6>
                <p class="mb-0">{{ session('success') }}</p>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" aria-label="Close" onclick="document.getElementById('welcomeAlert').remove()"></button>
    </div>
    @endif

    {{-- Statistik Box --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            @if ($kelas->isNotEmpty())
                <a href="{{ route('dosen.kelola_kelas.show', $kelas->first()->id) }}" class="text-decoration-none">
                    <div class="text-center p-4 rounded shadow-sm text-white" style="background-color: #f5a04e;">
                        <h3 class="fw-bold mb-1">{{ $userCount ?? 0 }}</h3>
                        <p class="mb-0">Users</p>
                    </div>
                </a>
            @endif
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('dosen.materi_kelas.index') }}" class="text-decoration-none">
                <div class="text-center p-4 rounded shadow-sm text-white" style="background-color: #f5a04e;">
                    <h3 class="fw-bold mb-1">{{ $mataKuliahCount ?? 0 }}</h3>
                    <p class="mb-0">Mata kuliah</p>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('dosen.materi_kelas.index') }}" class="text-decoration-none">
                <div class="text-center p-4 rounded shadow-sm text-white" style="background-color: #f5a04e;">
                    <h3 class="fw-bold mb-1">{{ $materiCount ?? 0 }}</h3>
                    <p class="mb-0">Konten/materi</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Statistik dan Kelas --}}
    <div class="row">
        {{-- Grafik Nilai --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm rounded h-100 border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Statistik Rata-rata Nilai</h6>
                        <a href="{{ route('dosen.rekap_nilai.index') }}" class="btn btn-sm text-white" style="background-color: #f5a04e;">
                            Selengkapnya
                        </a>
                    </div>
                    <div class="chart-wrapper" style="height: 300px;">
                        <canvas id="nilaiChart" style="width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kelas --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm rounded h-100 border-0">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Kelas / Mata Kuliah</h6>
                        <a href="{{ route('dosen.kelola_kelas.index', $kelas->first()->id ?? 1) }}" class="btn btn-sm text-white" style="background-color: #f5a04e;">
                            Selengkapnya
                        </a>
                    </div>
                    @if($kelas->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($kelas as $kls)
                                <li class="mb-3 px-3 py-2 rounded border d-flex justify-content-between align-items-center"
                                    style="transition: background 0.3s ease;">
                                    <div>
                                        <strong><i class="fas fa-book me-2 text-orange"></i>{{ $kls->nama_matakuliah }}</strong><br>
                                        <small class="text-muted ms-4">{{ $kls->nama_kelas }}</small>
                                    </div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('nilaiChart').getContext('2d');
    const nilaiChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($statistikNilai->pluck('judul')) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($statistikNilai->pluck('rata')) !!},
                backgroundColor: '#f5a04e'
            }]
        },
        options: {
            indexAxis: 'y',
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.x + ' / 100'
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Nilai' }
                },
                y: {
                    ticks: { autoSkip: false }
                }
            }
        }
    });
</script>
@endpush
@endsection