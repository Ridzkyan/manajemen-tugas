@extends('layouts.dosen')

@section('page_title', 'Rekap Nilai')

@section('content')
<link href="{{ asset('css/backsite/dosen/rekap.css') }}" rel="stylesheet">

{{-- Judul Tengah --}}
<div class="rekap-title">
    <i class="bi bi-clipboard-data-fill"></i> Rekap Nilai
</div>

{{-- Dropdown Pilih Kelas --}}
<form method="GET" action="{{ route('dosen.rekap_nilai.index') }}" class="mb-3">
    <select name="kelas_id" class="select-modern" onchange="this.form.submit()">
        <option value="">-- Pilih Kelas --</option>
        @foreach($kelasList as $kelas)
            <option value="{{ $kelas->id }}" {{ $selectedKelasId == $kelas->id ? 'selected' : '' }}>
                {{ $kelas->nama_matakuliah }} ({{ $kelas->nama_kelas }})
            </option>
        @endforeach
    </select>
</form>

{{-- Alert jika belum memilih kelas --}}
@if(!$selectedKelasId)
    <div class="alert-custom">
        <i class="bi bi-info-circle-fill"></i> 
        Silakan pilih kelas terlebih dahulu untuk melihat rekap nilai mahasiswa.
    </div>
@endif

{{-- Tombol Export dan Tabel Nilai --}}
@if($selectedKelasId && isset($tugas) && $tugas->isNotEmpty())
    {{-- Tombol Export Excel --}}
    <a href="{{ route('dosen.rekap_nilai.export', $selectedKelasId) }}" class="btn-export mb-3">
        <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
    </a>

    {{-- Tabel Nilai --}}
    <div class="table-responsive">
        <table class="table custom-table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Kelas</th>
                    <th>MK</th>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Nilai</th>
                    <th>Feedback</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tugas as $tgs)
                    @foreach($tgs->pengumpulanTugas as $kumpul)
                        <tr>
                            <td>{{ $kumpul->mahasiswa->name ?? '-' }}</td>
                            <td>{{ $tgs->kelas->nama_kelas ?? '-' }}</td>
                            <td>{{ $tgs->kelas->nama_matakuliah ?? '-' }}</td>
                            <td>{{ $tgs->judul }}</td>
                            <td>{{ ucfirst($tgs->tipe) }}</td>
                            <td>{{ $kumpul->nilai ?? 'Belum dinilai' }}</td>
                            <td>{{ $kumpul->feedback ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($tgs->deadline)->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@elseif($selectedKelasId && (!isset($tugas) || $tugas->isEmpty()))
    {{-- Alert jika kelas dipilih tapi tidak ada data tugas --}}
    <div class="alert-custom">
        <i class="bi bi-info-circle-fill"></i> 
        Belum ada data tugas untuk kelas yang dipilih.
    </div>
@endif

@endsection