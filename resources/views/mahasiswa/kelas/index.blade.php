@extends('layouts.mahasiswa')
@section('title', 'Kelas Saya')

@section('content')
<style>
    .card-kelas {
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 20px;
        height: 100%;
        transition: 0.3s ease;
    }

    .card-kelas:hover {
        transform: scale(1.01);
    }

    .btn-masuk {
        background-color: #007bff;
        color: #fff;
        font-weight: 500;
        border-radius: 20px;
        padding: 6px 16px;
    }

    .btn-keluar {
        border: 1px solid #dc3545;
        color: #dc3545;
        border-radius: 20px;
        padding: 6px 16px;
        font-weight: 500;
    }

    .btn-keluar:hover {
        background-color: #dc3545;
        color: #fff;
    }

    .kelas-header {
        font-size: 18px;
        font-weight: bold;
    }

    .kelas-sub {
        margin-bottom: 8px;
    }
</style>

<div class="container py-3">
    <h3 class="mb-4">📚 Daftar Kelas yang Kamu Ikuti</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($kelasmahasiswa->count())
        <div class="row">
            @foreach($kelasmahasiswa as $kelas)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card-kelas h-100">
                        <div class="kelas-header">{{ $kelas->nama_matakuliah }}</div>
                        <div class="kelas-sub"><strong>Kode:</strong> {{ $kelas->kode_unik }}</div>
                        <div class="kelas-sub"><strong>Dosen:</strong> {{ $kelas->dosen->name ?? 'Tidak diketahui' }}</div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="btn btn-masuk">
                                Masuk Kelas
                            </a>

                            <form action="{{ route('mahasiswa.kelas.leave', $kelas->id) }}" method="POST" onsubmit="return confirm('Yakin ingin keluar dari kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-keluar">Keluar</button>
                            </form>
                        </div>z
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
