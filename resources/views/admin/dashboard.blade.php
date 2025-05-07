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
    <div class="col-md-4 col-sm-12 mb-3">
        <a href="/admin/users" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">100</div>
                <div class="stat-label">Users</div>
            </div>
            <i class="fas fa-users card-icon"></i>
        </a>
    </div>
    <div class="col-md-4 col-sm-12 mb-3">
        <a href="/admin/matkul" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">50</div>
                <div class="stat-label">Mata Kuliah</div>
            </div>
            <i class="fas fa-book card-icon"></i>
        </a>
    </div>
    <div class="col-md-4 col-sm-12 mb-3">
        <a href="/admin/konten" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">150</div>
                <div class="stat-label">Konten/Materi</div>
            </div>
            <i class="fas fa-file-alt card-icon"></i>
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
            <li><b>Pemrograman Web</b><br>INF123 - Nama dosen</li>
            <li><b>Pemrograman Web Lanjut</b><br>INF124 - Nama dosen</li>
            <li><b>Statistika</b><br>INF125 - Nama dosen</li>
            <li><b>Kalkulus</b><br>INF126 - Nama dosen</li>
        </ul>
    </div>
    <div class="dashboard-card">
        <h5>Aktivitas pengguna teratas <a href="#" class="btn">Selengkapnya</a></h5>
        <div class="chart-wrapper">
            <canvas id="barChart"></canvas>
        </div>
    </div>
    <div class="dashboard-card">
        <h5>Konten Terbaru <a href="#" class="btn">Selengkapnya</a></h5>
        <ul>
            <li><b>Materi 1</b><br>Nama matakuliah - Kode</li>
            <li><b>Materi 2</b><br>Nama matakuliah - Kode</li>
            <li><b>Materi 3</b><br>Nama matakuliah - Kode</li>
            <li><b>Materi 4</b><br>Nama matakuliah - Kode</li>
            <li><b>Materi 5</b><br>Nama matakuliah - Kode</li>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
    Chart.register(ChartDataLabels);

    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: ['Dosen', 'Mahasiswa', 'Admin'],
            datasets: [{
                data: [12, 80, 8],
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
            labels: ['Statistika', 'Web Lanjut', 'Kalkulus', 'Jaringan Komputer', 'Matematika Diskrit'],
            datasets: [{
                label: 'Jumlah Aktivitas',
                data: [38, 5, 15, 28, 12],
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