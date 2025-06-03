@extends('layouts.mahasiswa')
@section('title', 'Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil Login!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .rounded-20 { border-radius: 20px; }
    .shadow-soft { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .card-title-section { font-weight: bold; font-size: 1rem; color: #333; }
    .card-small-btn { font-size: 0.8rem; }
    .table-sm th, .table-sm td { vertical-align: middle; }
    .chart-container { width: 100%; max-width: 100%; height: 300px; position: relative; }
    @media (max-width: 768px) {
        .chart-container { height: 250px; }
    }
</style>

<div class="mb-4 p-4 bg-warning text-white shadow-soft rounded-20">
    <h5 class="fw-bold mb-1">Halo, {{ Auth::user()->name ?? 'Nama Mahasiswa' }}!</h5>
    <p class="mb-0">Selamat datang, mari kita mulai hari dengan semangat dan produktivitas tinggi.</p>
</div>

<div class="mb-4 p-4 bg-white shadow-soft rounded-20">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="chart-container">
                <canvas id="tugasChart"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex flex-column justify-content-between h-100">
                <div>
                    <h6 class="card-title-section mb-3">Statistik Pengumpulan Tugas</h6>
                    <ul class="list-unstyled small">
                        <li><span class="badge bg-success me-2">Terkumpul</span> Jumlah tugas berhasil dikumpulkan</li>
                        <li><span class="badge bg-danger me-2">Belum</span> Jumlah tugas belum dikumpulkan</li>
                    </ul>

                    <div class="btn-group btn-group-sm mt-3" role="group">
                        <button class="btn btn-outline-secondary" onclick="renderChart('harian')">Harian</button>
                        <button class="btn btn-outline-secondary" onclick="renderChart('mingguan')">Mingguan</button>
                        <button class="btn btn-outline-secondary" onclick="renderChart('bulanan')">Bulanan</button>
                    </div>

                    @php
                        $user = Auth::guard('mahasiswa')->user();
                        $kelasAktifId = $kelasId ?? optional($user->kelasMahasiswa()->first())->id;
                    @endphp
                    @if($kelasAktifId)
                        <a href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $kelasAktifId]) }}"
                           class="btn btn-sm btn-outline-warning bg-warning text-white shadow-soft rounded-20 card-small-btn mt-3">
                            Selengkapnya
                        </a>
                    @else
                        <a class="btn btn-sm btn-outline-warning bg-warning text-white shadow-soft rounded-20 card-small-btn mt-3 disabled"
                           tabindex="-1" aria-disabled="true">
                            Selengkapnya
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const dataStatistik = @json($statistik);

    function renderChart(mode = 'harian') {
        const ctx = document.getElementById('tugasChart').getContext('2d');

        if (window.tugasChart instanceof Chart) {
            window.tugasChart.destroy();
        }

        const data = dataStatistik[mode];
        const belum = Math.max((data.total_tugas || 0) - (data.terkumpul || 0), 0);

        window.tugasChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Tugas'],
                datasets: [
                    {
                        label: 'Terkumpul',
                        data: [data.terkumpul],
                        backgroundColor: 'rgba(0, 128, 0, 0.7)',
                        borderRadius: 8
                    },
                    {
                        label: 'Belum Terkumpul',
                        data: [belum],
                        backgroundColor: 'rgba(255, 0, 0, 0.7)',
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#444',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#333' },
                        grid: { color: '#eee' }
                    },
                    x: {
                        ticks: { color: '#333' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => renderChart('harian'));
</script>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-soft rounded-20 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title-section">Kelas / Mata Kuliah</div>
                    <a href="{{ route('mahasiswa.kelas.index') }}" class="btn btn-sm btn-outline-warning bg-warning text-white shadow-soft rounded-20 card-small-btn">Selengkapnya</a>
                </div>
                @if($kelasmahasiswa->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($kelasmahasiswa as $kelas)
                            <li class="mb-3 border-bottom pb-2">
                                <strong>{{ $kelas->nama_matakuliah }}</strong><br>
                                <small class="text-muted">{{ $kelas->kode_kelas ?? '-' }} - {{ optional($kelas->dosen)->name ?? 'Dosen Tidak Dikenal' }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum bergabung ke kelas manapun.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-soft rounded-20 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title-section">Daftar Tugas Aktif</div>
                    @if($tugasAktif->count() && $tugasAktif->pluck('kelas_id')->unique()->count() === 1)
                        <a href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $kelasAktifId]) }}" class="btn btn-sm btn-outline-warning bg-warning text-white shadow-soft rounded-20 card-small-btn">
                            Selengkapnya
                        </a>
                    @else
                        <span class="text-muted small">Pilih kelas untuk melihat detail</span>
                    @endif
                </div>
                @if($tugasAktif->count())
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kelas</th>
                                <th>Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tugasAktif as $tugas)
                                <tr>
                                    <td>
                                        <a href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $tugas->kelas_id]) }}" class="text-decoration-none text-dark fw-semibold">
                                            {{ $tugas->judul }}
                                        </a><br>
                                        @if(in_array($tugas->id, $tugasSudahDikumpulkan))
                                            <span class="badge bg-success">Terkumpul</span>
                                        @else
                                            <span class="badge bg-danger">Belum</span>
                                        @endif
                                    </td>
                                    <td>{{ $tugas->kelas->nama_matakuliah ?? '-' }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($tugas->deadline)->format('Y-m-d') }}<br>
                                        <small class="text-danger">
                                            {{ \Carbon\Carbon::parse($tugas->deadline)->diffForHumans(now(), ['parts' => 2]) }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Tidak ada tugas aktif saat ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
