@extends('layouts.mahasiswa')
@section('title', 'Dashboard')

@section('content')
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="card-title-section">Statistik IPS/IPK</div>
        <a href="#" class="btn btn-sm btn-outline-warning card-small-btn">Selengkapnya</a>
    </div>
    <div class="chart-placeholder d-flex justify-content-center align-items-center text-muted">
        (Grafik IPS & IPK Placeholder)
    </div>
</div>

<div class="row">
    {{-- Kelas / Mata Kuliah --}}
    <div class="col-md-6 mb-4">
        <div class="card shadow-soft rounded-20 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title-section">Kelas / Mata Kuliah</div>
                    <a href="{{ route('mahasiswa.kelas.index') }}" class="btn btn-sm btn-outline-warning card-small-btn">Selengkapnya</a>
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
