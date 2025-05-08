@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    .card-info {
        background-color: #f5a04e;
        color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        height: 130px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        transition: transform 0.3s;
    }

    .card-info:hover {
        transform: translateY(-5px);
    }

    .card-left {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stat-number {
        font-size: 22px;
        font-weight: bold;
    }

    .stat-label {
        font-size: 14px;
        margin-top: 2px;
    }

    .card-icon {
        font-size: 28px;
        opacity: 0.9;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .dashboard-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 20px;
        display: flex;
        flex-direction: column;
        height: 300px;
        overflow: hidden;
    }

    .dashboard-card h5 {
        font-weight: bold;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dashboard-card .btn {
        font-size: 0.75rem;
        background-color: #f5a04e;
        border-radius: 50px;
        color: white;
        padding: 3px 12px;
    }

    .dashboard-card ul {
        padding-left: 1rem;
        font-size: 14px;
    }

    .dashboard-card .chart-wrapper {
        position: relative;
        flex-grow: 1;
    }

    .dashboard-card canvas {
        position: absolute;
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
        .card-info {
            flex-direction: column;
            height: auto;
            text-align: center;
        }
        .card-icon {
            margin-top: 10px;
        }
    }
</style>

<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <a href="/admin/users" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">{{ $totalUsers }}</div>
                <div class="stat-label">Users</div>
            </div>
            <i class="fas fa-users card-icon"></i>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <a href="/admin/kelas" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">{{ $totalKelas }}</div>
                <div class="stat-label">Kelas</div>
            </div>
            <i class="fas fa-chalkboard card-icon"></i>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <a href="/admin/materi" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">{{ $totalMateri }}</div>
                <div class="stat-label">Materi</div>
            </div>
            <i class="fas fa-file-alt card-icon"></i>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <a href="/admin/tugas" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">{{ $totalTugas }}</div>
                <div class="stat-label">Tugas</div>
            </div>
            <i class="fas fa-tasks card-icon"></i>
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <h5>Data users <a href="#" class="btn">Selengkapnya</a></h5>
        <div class="chart-wrapper">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
    <div class="dashboard-card">
        <h5>Kelas/Mata kuliah <a href="#" class="btn">Selengkapnya</a></h5>
        <ul>
        @foreach($daftarKelas as $kelas)
            <li>
                <b>{{ $kelas->nama_kelas }} - {{ $kelas->nama_matakuliah }}</b><br>
                {{ $kelas->kode_unik }} - {{ $kelas->dosen->name ?? 'Dosen Tidak Ada' }}
            </li>
        @endforeach
        </ul>
    </div>
    <div class="dashboard-card">
        <h5>Aktivitas pengguna teratas <a href="{{ route('admin.monitoring') }}" class="btn">Selengkapnya</a></h5>
        <div class="chart-wrapper">
            <canvas id="barChart"></canvas>
        </div>
    </div>
    <div class="dashboard-card">
        <h5>Konten Terbaru <a href="#" class="btn">Selengkapnya</a></h5>
        <ul>
        @foreach($materiTerbaru as $materi)
            <li>
                <b>{{ $materi->judul }}</b><br>
                {{ $materi->kelas->nama_matakuliah ?? 'Mata kuliah tidak tersedia' }} - {{ $materi->kelas->kode_unik ?? 'Kode kosong' }}
            </li>
        @endforeach
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    Chart.register(ChartDataLabels);

    const pieChart = new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: ['Dosen', 'Mahasiswa', 'Admin'],
            datasets: [{
                data: [{{ $jumlahDosen }}, {{ $jumlahMahasiswa }}, {{ $jumlahAdmin }}],
                backgroundColor: ['#007bff', '#dc3545', '#ffc107'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        let percentage = (value * 100 / sum).toFixed(0) + "%";
                        return percentage;
                    },
                    font: {
                        weight: 'bold'
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($kelasTeraktif->pluck('label')) !!},
            datasets: [{
                label: 'Jumlah Materi',
                data: {!! json_encode($kelasTeraktif->pluck('materi_count')) !!},
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'right',
                    color: '#000',
                    formatter: value => value,
                    font: { weight: 'bold' }
                }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

</script>
@endsection