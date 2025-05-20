@extends('layouts.mahasiswa')
@section('title', 'Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .rounded-20 {
        border-radius: 20px;
    }

    .shadow-soft {
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .chart-placeholder {
        height: 200px;
        background: linear-gradient(to right, #cde, #eef);
        border-radius: 10px;
    }

    .card-title-section {
        font-weight: bold;
        font-size: 1rem;
        color: #333;
    }

    .card-small-btn {
        font-size: 0.8rem;
    }

    .table-sm th, .table-sm td {
        vertical-align: middle;
    }
</style>

{{-- Welcome Box --}}
<div class="mb-4 p-4 bg-warning text-white shadow-soft rounded-20">
    <h5 class="fw-bold mb-1">Halo, {{ Auth::user()->name ?? 'Nama Mahasiswa' }}!</h5>
    <p class="mb-0">Selamat datang, mari kita mulai hari dengan semangat dan produktivitas tinggi.</p>
</div>

{{-- Statistik IPS/IPK --}}
<div class="mb-4 p-4 bg-white shadow-soft rounded-20">
    <div class="row align-items-center">
        <div class="col-md-8">
            <canvas id="ipsIpkChart" height="100"></canvas>
        </div>
        <div class="col-md-4">
            <div class="d-flex flex-column justify-content-between h-100">
                <div>
                    <h6 class="card-title-section mb-3">Statistik IPS/IPK</h6>
                    <ul class="list-unstyled small">
                        <li><span class="badge bg-primary me-2">IPS</span> Indeks Prestasi Semester</li>
                        <li><span class="badge bg-danger me-2">IPK</span> Indeks Prestasi Kumulatif</li>
                    </ul>
                </div>
                    <a href="{{ route('mahasiswa.kelas.index') }}" class="btn btn-sm btn-outline-warning bg-warning text-white shadow-soft rounded-20 card-small-btn w-">Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ipsIpkChart').getContext('2d');

    const ipsIpkChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['1', '2', '3', '4'], // Semester
            datasets: [
                {
                    label: 'IPS',
                    data: [3.86, 3.86, 3.94, 4.00],
                    borderColor: 'blue',
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    fill: false,
                },
                {
                    label: 'IPK',
                    data: [3.86, 3.86, 3.87, 3.94],
                    borderColor: 'red',
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
            },
            scales: {
                y: {
                    min: 3.75,
                    max: 4.05,
                    ticks: {
                        stepSize: 0.05,
                    }
                },
                x: {
                    title: {
                        display: false,
                        text: 'Semester',
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
</script>


    <div class="row">
       {{-- Kelas / Mata Kuliah --}}
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

{{-- Daftar Tugas Aktif --}}
<div class="col-md-6 mb-4">
    <div class="card shadow-soft rounded-20 h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="card-title-section">Daftar Tugas Aktif</div>
                @if($tugasAktif->count() && $tugasAktif->pluck('kelas_id')->unique()->count() === 1)
                    <a href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $tugasAktif->first()->kelas_id]) }}" class="btn btn-sm btn-outline-warning card-small-btn">Selengkapnya</a>
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
                                        <span class="badge bg-success">✅ Terkumpul</span>
                                    @else
                                        <span class="badge bg-danger">❌ Belum</span>
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