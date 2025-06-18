
@extends('layouts.mahasiswa')
@section('title', 'Dashboard')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('login_success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil Login!',
        text: '{{ session('login_success') }}',
        showConfirmButton: false,
        timer: 2000
    });
</script>
@endif

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    .rounded-20 { border-radius: 20px; }
    .shadow-soft { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .card-title-section { font-weight: bold; font-size: 1rem; color: #333; }
    .card-small-btn { font-size: 0.8rem; }
    .table-sm th, .table-sm td { vertical-align: middle; }
    
    .stat-card {
        color: white;
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .stat-number {
        font-size: 2.8rem;
        font-weight: bold;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        line-height: 1;
    }
    .stat-label {
        font-size: 1rem;
        opacity: 0.9;
        margin-top: 0.5rem;
    }
    .stat-icon {
        opacity: 0.8;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .stat-number { font-size: 2.2rem; }
        .stat-label { font-size: 0.9rem; }
    }
</style>

<div class="mb-4 p-4 bg-warning text-white shadow-soft rounded-20">
    <h5 class="fw-bold mb-1">Halo, {{ Auth::user()->name ?? 'Nama Mahasiswa' }}!</h5>
    <p class="mb-0">Selamat datang, mari kita mulai hari dengan semangat dan produktivitas tinggi.</p>
</div>

<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card shadow-soft h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-center p-4">
                <i class="fas fa-tasks fa-2x stat-icon"></i>
                <div class="stat-number">
                    {{ $statistik['total']['total_tugas'] ?? 0 }}
                </div>
                <div class="stat-label">Total Tugas</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card shadow-soft h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body text-center p-4">
                <i class="fas fa-check-circle fa-2x stat-icon"></i>
                <div class="stat-number">
                    {{ $statistik['total']['terkumpul'] ?? 0 }}
                </div>
                <div class="stat-label">Sudah Dikumpulkan</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card shadow-soft h-100" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);">
            <div class="card-body text-center p-4">
                <i class="fas fa-clock fa-2x stat-icon"></i>
                @php
                    $belum = max(($statistik['total']['total_tugas'] ?? 0) - ($statistik['total']['terkumpul'] ?? 0), 0);
                @endphp
                <div class="stat-number">{{ $belum }}</div>
                <div class="stat-label">Belum Dikumpulkan</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action untuk Statistik -->
<div class="mb-4 text-center">
    @php
        $user = Auth::guard('mahasiswa')->user();
        $kelasAktifId = $kelasId ?? optional($user->kelasMahasiswa()->first())->id;
    @endphp
    @if($kelasAktifId)
        <a href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $kelasAktifId]) }}"
           class="btn btn-outline-warning bg-warning text-white shadow-soft rounded-20">
            <i class="fas fa-list me-2"></i>Lihat Semua Tugas
        </a>
    @else
        <a class="btn btn-outline-warning bg-warning text-white shadow-soft rounded-20 disabled"
           tabindex="-1" aria-disabled="true">
            <i class="fas fa-list me-2"></i>Lihat Semua Tugas
        </a>
    @endif
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-soft rounded-20 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title-section">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>Kelas / Mata Kuliah
                    </div>
                    <a href="{{ route('mahasiswa.kelas.index') }}" class="btn btn-sm btn-outline-warning bg-warning text-white shadow-soft rounded-20 card-small-btn">Selengkapnya</a>
                </div>
                @if($kelasmahasiswa->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($kelasmahasiswa as $kelas)
                            <li class="mb-3 border-bottom pb-2">
                                <strong>{{ $kelas->nama_matakuliah }}</strong><br>
                                <small class="text-muted">
                                    <i class="fas fa-code me-1"></i>{{ $kelas->kode_kelas ?? '-' }} - 
                                    <i class="fas fa-user me-1"></i>{{ optional($kelas->dosen)->name ?? 'Dosen Tidak Dikenal' }}
                                </small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                        <p>Belum bergabung ke kelas manapun.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-soft rounded-20 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title-section">
                        <i class="fas fa-clipboard-list me-2 text-danger"></i>Daftar Tugas Aktif
                    </div>
                    @if($tugasAktif->count() && $tugasAktif->pluck('kelas_id')->unique()->count() === 1)
                        <a href="{{ route('mahasiswa.kelas.tugas.index', ['kelas' => $kelasAktifId]) }}" class="btn btn-sm btn-outline-warning bg-warning text-white shadow-soft rounded-20 card-small-btn">
                            Selengkapnya
                        </a>
                    @else
                        <span class="text-muted small">Pilih kelas untuk melihat detail</span>
                    @endif
                </div>
                @if($tugasAktif->count())
                    <div class="table-responsive">
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
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Terkumpul
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $tugas->kelas->nama_matakuliah ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($tugas->deadline)->format('d/m/Y') }}</strong><br>
                                            <small class="text-danger">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($tugas->deadline)->diffForHumans(now(), ['parts' => 2]) }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-check fa-3x mb-3 opacity-50"></i>
                        <p>Tidak ada tugas aktif saat ini.</p>
                        <small>Selamat! Anda sudah menyelesaikan semua tugas.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection