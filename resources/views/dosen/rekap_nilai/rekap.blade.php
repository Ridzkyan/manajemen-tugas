@extends('layouts.dosen')

@section('page_title', 'Rekap Nilai')

@section('content')
<style>
    .rekap-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 32px;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .rekap-title i {
        color: #f4c430;
        font-size: 1.6rem;
    }

        .select-modern {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #ccc;
        background-color: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        font-size: 1rem;
        appearance: none;
        background-image: url('data:image/svg+xml;utf8,<svg fill="gray" height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position-x: 95%;
        background-position-y: 50%;
        background-size: 12px;
    }

    .select-modern:focus {
        outline: none;
        border-color: #f5a04e;
        box-shadow: 0 0 0 3px rgba(245, 160, 78, 0.25);
    }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        margin-bottom: 20px;
        background-color: #198754;
        border: none;
        border-radius: 8px;
        color: white;
        padding: 10px 16px;
        transition: background 0.3s ease;
    }

    .btn-export:hover {
        background-color: #157347;
        color: white;
        text-decoration: none;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .custom-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 14px;
        text-align: center;
    }

    .custom-table td {
        padding: 12px;
        text-align: center;
    }

    .custom-table tbody tr:nth-child(even) {
        background-color: #fdfdfd;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .alert-custom {
        background-color: #fffbea;
        border-left: 5px solid #ffc107;
        padding: 16px 20px;
        border-radius: 10px;
        color: #7c5a00;
        font-size: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-top: 12px;
    }
</style>

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
                {{ $kelas->nama_kelas }} ({{ $kelas->nama_matakuliah }})
            </option>
        @endforeach
    </select>
</form>

{{-- Tombol Export + Tabel --}}
@if($tugas->isNotEmpty())
    @if($selectedKelasId)
        <a href="{{ route('dosen.rekap_nilai.export', $selectedKelasId) }}" class="btn-export">
            <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
        </a>
    @endif

    <div class="table-responsive">
        <table class="table custom-table">
            <thead>
                <tr>
                    <th>Judul Tugas</th>
                    <th>Nilai</th>
                    <th>Feedback</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tugas as $tgs)
                    <tr>
                        <td>
                            <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i>
                            {{ $tgs->judul }}
                        </td>
                        <td>{{ $tgs->nilai ?? 'Belum dinilai' }}</td>
                        <td>{{ $tgs->feedback ?? '-' }}</td>
                        <td>
                            <i class="bi bi-calendar-event-fill text-primary me-1"></i>
                            {{ $tgs->deadline ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert-custom">
        <i class="bi bi-info-circle-fill"></i> Silakan pilih kelas untuk melihat rekap nilai.
    </div>
@endif
@endsection