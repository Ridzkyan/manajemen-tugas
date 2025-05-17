@extends('layouts.dosen')

@section('page_title', 'Rekap Nilai')

@section('content')
<style>
    .rekap-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rekap-title i {
        color: #f4c430;
        font-size: 1.4rem;
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

    .alert-info-ui {
        display: flex;
        align-items: center;
        gap: 12px;
        background-color: #fffbea;
        border-left: 5px solid #ffc107;
        padding: 16px 20px;
        border-radius: 10px;
        color: #7c5a00;
        font-size: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .alert-info-ui i {
        font-size: 1.4rem;
        color: #ffc107;
    }
</style>

<div class="rekap-title">
    <i class="fas fa-clipboard-list"></i> Rekap Nilai
</div>

<form method="GET" action="{{ route('dosen.rekap_nilai.index') }}">
    <select name="kelas_id" class="select-modern" onchange="this.form.submit()">
        <option value="">-- Pilih Kelas --</option>
        @foreach($kelas as $kls)
            <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                {{ $kls->nama_matakuliah }} ({{ $kls->nama_kelas }})
            </option>
        @endforeach
    </select>
</form>

@if(!request('kelas_id'))
    <div class="alert-info-ui">
        <i class="bi bi-info-circle-fill"></i>
        Silakan pilih kelas terlebih dahulu untuk melihat rekap nilai mahasiswa.
    </div>
@endif
@endsection