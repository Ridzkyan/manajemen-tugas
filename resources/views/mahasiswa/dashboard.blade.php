@extends('layouts.mahasiswa')
@section('title', 'Dashboard')

@section('content')
{{-- Welcome Section --}}
<div class="mb-4 p-3 bg-warning rounded text-white shadow-sm">
    <h5>Halo, {{ Auth::user()->nama ?? 'Nama Mahasiswa' }}!</h5>
    <p>Selamat datang, mari kita mulai hari dengan semangat dan produktivitas tinggi.</p>
</div>

{{-- 2 Card Utama --}}
<div class="row">
    {{-- Card Kelas --}}
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Kelas / Mata Kuliah</h6>
                    {{-- <a href="{{ route('mahasiswa.kelas.materi') }}" class="btn btn-sm btn-outline-warning">Selengkapnya</a> --}}
                </div>

                @if($kelasmahasiswa->count())
                    <ul class="list-unstyled mb-0">
                        @foreach($kelasmahasiswa as $kelas)
                            <li class="mb-3">
                                <strong>{{ $kelas->nama_matakuliah }}</strong><br>
                                <small>{{ $kelas->kode_kelas ?? '-' }} - {{ optional($kelas->dosen)->name ?? 'Dosen Tidak Dikenal' }}</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum bergabung ke kelas manapun.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Card Tugas --}}
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Daftar Tugas Aktif</h6>
                    {{-- - <a href="{{ route('mahasiswa.kelas.tugas') }}" class="btn btn-sm btn-outline-warning">Selengkapnya</a>--}}
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
                                    {{ $tugas->judul }}
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
