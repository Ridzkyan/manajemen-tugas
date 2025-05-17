@extends('layouts.mahasiswa')
@section('title', 'Kelas Saya')

@section('content')
<div class="container">
    <h3 class="mb-4">📚 Daftar Kelas yang Kamu Ikuti</h3>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Daftar kelas --}}
    @if($kelasmahasiswa->count())
        <div class="row">
            @foreach($kelasmahasiswa as $kelas)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $kelas->nama_matakuliah }}</h5>
                            <p class="mb-1"><strong>Kode Kelas:</strong> {{ $kelas->kode_unik }}</p>
                            <p class="mb-2"><strong>Dosen:</strong> {{ $kelas->dosen->name ?? 'Tidak diketahui' }}</p>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="btn btn-sm btn-primary">
                                    Masuk Kelas
                                </a>

                                {{-- Form keluar kelas --}}
                                <form action="{{ route('mahasiswa.kelas.leave', $kelas->id) }}" method="POST" onsubmit="return confirm('Yakin ingin keluar dari kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Kamu belum bergabung dengan kelas manapun.</div>
        <a href="{{ route('mahasiswa.join.index') }}" class="btn btn-success">🔑 Gabung ke Kelas</a>
    @endif
</div>
@endsection
