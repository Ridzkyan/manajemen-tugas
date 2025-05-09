@extends('layouts.mahasiswa')

@section('content')
<div class="container">
    <h3>Dashboard Mahasiswa</h3>

    {{-- Alert Success/Error --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Tombol Arahkan ke Join Kelas --}}
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Belum punya kelas? Gabung sekarang!</h5>
            <a href="{{ route('mahasiswa.join.index') }}" class="btn btn-success">
                ➕ Gabung ke Kelas
            </a>
        </div>
    </div>

    {{-- Daftar Kelas Yang Diikuti --}}
    <div class="card">
        <div class="card-header">
            Kelas Yang Kamu Ikuti
        </div>
        <div class="card-body">
            @if($kelasmahasiswa->count() > 0)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen Pengajar</th>
                            <th>Grup WhatsApp</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelasmahasiswa as $kelas)
                        <tr>
                            <td>{{ $kelas->nama_kelas }}</td>
                            <td>{{ $kelas->nama_matakuliah }}</td>
                            <td>{{ optional($kelas->dosen)->name ?? 'Tidak diketahui' }}</td>
                            <td>
                                @if($kelas->whatsapp_link)
                                    <a href="{{ $kelas->whatsapp_link }}" target="_blank" class="btn btn-success btn-sm">Join WhatsApp</a>
                                @else
                                    <span class="text-muted">Tidak ada link</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="btn btn-primary btn-sm">Masuk</a>
                                <form action="{{ route('mahasiswa.kelas.leave', $kelas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin keluar dari kelas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Keluar</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>                            
                </table>
            @else
                <p class="text-muted">Kamu belum bergabung ke kelas manapun.</p>
            @endif
        </div>
    </div>
</div>
@endsection
