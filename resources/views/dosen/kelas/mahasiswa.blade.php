@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Mahasiswa di Kelas: {{ $kelas->nama_kelas }}</h3>

    {{-- Alert Success/Error --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Daftar Mahasiswa --}}
    <div class="card">
        <div class="card-header">
            Daftar Mahasiswa
        </div>
        <div class="card-body">
            @if($mahasiswa->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <th>Email</th>
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
    </div>

</div>
@endsection
