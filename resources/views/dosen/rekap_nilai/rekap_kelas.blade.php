@extends('layouts.dosen')

@section('content')
<div class="container">
    <h4 class="mb-4">Rekap Nilai - Pilih Kelas</h4>
    <ul class="list-group">
        @foreach($kelas as $kls)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $kls->nama_matakuliah }} ({{ $kls->nama_kelas }})</span>
                <a href="{{ route('dosen.rekap.detail', $kls->id) }}" class="btn btn-sm btn-primary">Lihat Rekap</a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
