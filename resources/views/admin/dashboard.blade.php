@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/backsite/admin/dashboard.css') }}">

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
        <a href="/admin/konten" class="card-info text-decoration-none">
            <div class="card-left">
                <div class="stat-number">{{ $totalMateri }}</div>
                <div class="stat-label">Materi</div>
            </div>
            <i class="fas fa-file-alt card-icon"></i>
        </a>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <a href="/admin/konten" class="card-info text-decoration-none">
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
        <h5>Data users <a href="users" class="btn">Selengkapnya</a></h5>
        <div class="chart-wrapper">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
    <div class="dashboard-card">
        <h5>Kelas/Mata kuliah <a href="{{ route('admin.kelas.index') }}" class="btn">Selengkapnya</a></h5>
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
        <h5>Konten Terbaru <a href="{{ route('admin.konten.index') }}" class="btn">Selengkapnya</a></h5>
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
    window.chartData = {
        pie: {
            dosen: {{ $jumlahDosen }},
            mahasiswa: {{ $jumlahMahasiswa }},
            admin: {{ $jumlahAdmin }}
        },
        bar: {
            labels: {!! json_encode($kelasTeraktif->pluck('label')) !!},
            data: {!! json_encode($kelasTeraktif->pluck('materi_count')) !!}
        }
    };

    Chart.register(ChartDataLabels);

    const pieChart = new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: ['Dosen', 'Mahasiswa', 'Admin'],
            datasets: [{
                data: [
                    window.chartData.pie.dosen,
                    window.chartData.pie.mahasiswa,
                    window.chartData.pie.admin
                ],
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
            labels: window.chartData.bar.labels,
            datasets: [{
                label: 'Jumlah Materi',
                data: window.chartData.bar.data,
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

    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (toggleBtn && sidebar && overlay) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                sidebar.classList.toggle('hide');
                toggleBtn.classList.toggle('active');
                overlay.classList.toggle('show');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                sidebar.classList.add('hide');
                toggleBtn.classList.remove('active');
                overlay.classList.remove('show');
            });
        }
    });
</script>
@endsection
