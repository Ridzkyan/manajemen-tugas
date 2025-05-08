@extends('layouts.admin')

@section('title', 'Kelas / Mata Kuliah')

@section('content')
<div class="container py-4">
    <h4 class="mb-4">Daftar Kelas / Mata Kuliah</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <strong>Data Kelas</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Kelas</th>
                    <th>Mata Kuliah</th>
                    <th>Dosen</th>
                    <th>Kode Unik</th>
                    <th>Mahasiswa</th>
                    <th>Materi</th>
                    <th>Tugas</th>
                    <th>Grup WA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daftarKelas as $index => $kelas)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $kelas->nama_kelas }}</td>
                        <td>{{ $kelas->nama_matakuliah }}</td>
                        <td>{{ $kelas->dosen->name ?? '-' }}</td>
                        <td>{{ $kelas->kode_unik }}</td>
                        <td>{{ $kelas->mahasiswa->count() }}</td>
                        <td>{{ $kelas->materi->count() }}</td>
                        <td>{{ $kelas->tugas->count() }}</td>
                        <td>
                            @if($kelas->whatsapp_link)
                                <a href="{{ $kelas->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success">Buka</a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
