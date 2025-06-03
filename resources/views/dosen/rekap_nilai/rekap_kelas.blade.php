@extends('layouts.dosen')

@section('page_title', 'Rekap Nilai')
@section('content')

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