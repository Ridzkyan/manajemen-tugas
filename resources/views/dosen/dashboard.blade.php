@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    {{-- Welcome Box --}}
    <div class="alert alert-warning rounded shadow-sm text-dark fs-5">
        <strong>Halo, {{ Auth::user()->nama ?? 'Nama Dosen' }}!</strong><br>
        Selamat datang, mari kita mulai hari dengan semangat dan produktivitas tinggi.
    </div>

    {{-- Statistik Box --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-white shadow-sm rounded">
                <div class="card-body text-center">
                    <h3>{{ $userCount ?? 0 }}</h3>
                    <p class="mb-0">Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white shadow-sm rounded">
                <div class="card-body text-center">
                    <h3>{{ $mataKuliahCount ?? 0 }}</h3>
                    <p class="mb-0">Mata Kuliah</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-orange text-white shadow-sm rounded">
                <div class="card-body text-center">
                    <h3>{{ $materiCount ?? 0 }}</h3>
                    <p class="mb-0">Konten/Materi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik + Tabel Ringkasan --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">
                    Statistik Rata-rata Nilai
                </div>
                <div class="card-body">
                    <canvas id="nilaiChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span>Kelas / Mata Kuliah</span>
                    <a href="{{ route('dosen.kelas.create') }}" class="btn btn-sm btn-warning text-white">
                        + Tambah Kelas
                    </a>
                </div>
                <div class="card-body">
                    @if($kelas->count() > 0)
                        <ul class="list-group">
                            @foreach($kelas as $kls)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $kls->nama_matakuliah }}</span>
                                    <small class="text-muted">{{ $kls->nama_kelas }}</small>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('dosen.kelas.index', $kelas->first()->id) }}" class="btn btn-sm btn-outline-primary mt-3 float-end">Selengkapnya</a>
                    @else
                        <p class="text-muted">Belum ada kelas yang dibuat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('nilaiChart').getContext('2d');
    const nilaiChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: @json(array_keys($statistikNilai ?? [])),
            datasets: [{
                data: @json(array_values($statistikNilai ?? [])),
                backgroundColor: [
                    '#9c27b0', '#2196f3', '#4caf50', '#ff9800', '#f44336'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
