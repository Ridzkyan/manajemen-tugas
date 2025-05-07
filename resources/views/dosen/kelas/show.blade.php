@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Daftar Mahasiswa di Kelas: {{ $kelas->nama_kelas }} - {{ $kelas->nama_matakuliah }}</h3>

    <a href="{{ route('dosen.kelas.index') }}" class="btn btn-secondary mb-3">Kembali ke Daftar Kelas</a>

    @if($mahasiswa->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>Email Mahasiswa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswa as $mhs)
                    <tr>
                        <td>{{ $mhs->name }}</td>
                        <td>{{ $mhs->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-muted">Belum ada mahasiswa yang bergabung ke kelas ini.</p>
    @endif
</div>
@endsection
