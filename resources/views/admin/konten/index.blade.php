@extends('layouts.admin')

@section('title', 'Konten Terbaru')

@section('content')
<style>
    .filter-bar {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 1rem;
    }

    .filter-bar select,
    .filter-bar input {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .konten-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .konten-table th,
    .konten-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        vertical-align: middle;
    }

    .konten-table th {
        background-color: #f8f8f8;
        font-weight: bold;
        text-align: left;
    }

    .btn-download {
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 20px;
        color: white;
        background-color: #28a745;
        border: none;
        text-decoration: none;
    }

    .btn-download:hover {
        background-color: #218838;
    }

    .btn-sm {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        border: none;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }
</style>

<div class="container py-4">
    <h4 class="mb-4">Konten Terbaru</h4>

    <div class="filter-bar mb-3">
        <select>
            <option>Semua matkul</option>
            {{-- Tambahkan filter jika ingin --}}
        </select>
        <input type="text" placeholder="Cari konten...">
    </div>

    <table class="konten-table">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Mata Kuliah</th>
                <th>Diunggah Oleh</th>
                <th>Jenis</th>
                <th>Download</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- === Materi === --}}
            @forelse($materiTerbaru as $materi)
                <tr>
                    <td>{{ $materi->judul }}</td>
                    <td>{{ $materi->kelas->nama_matakuliah ?? '-' }}</td>
                    <td>{{ $materi->kelas->dosen->name ?? '-' }}</td>
                    <td>Materi</td>
                    <td>
                        @if($materi->file)
                            <a href="{{ asset('storage/' . $materi->file) }}" target="_blank" class="btn-download mb-1 d-inline-block">PDF</a><br>
                        @endif

                        @if($materi->link)
                            <a href="{{ $materi->link }}" target="_blank" class="btn-download" style="background-color:#dc3545;">YouTube</a>
                        @endif

                        @if(!$materi->file && !$materi->link)
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($materi->status) }}</td>
                    <td>
                        @if($materi->status === 'menunggu')
                            <form action="{{ route('admin.konten.materi.setujui', $materi->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn btn-success btn-sm">Setujui</button>
                            </form>
                            <form action="{{ route('admin.konten.materi.tolak', $materi->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn btn-danger btn-sm">Tolak</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada materi terbaru.</td></tr>
            @endforelse

            {{-- === Tugas === --}}
            @forelse($tugasTerbaru as $tugas)
                <tr>
                    <td>{{ $tugas->judul }}</td>
                    <td>{{ $tugas->kelas->nama_matakuliah ?? '-' }}</td>
                    <td>{{ $tugas->kelas->dosen->name ?? '-' }}</td>
                    <td>Tugas</td>
                    <td>
                        @if($tugas->file_soal)
                            <a href="{{ asset('storage/' . $tugas->file_soal) }}" target="_blank" class="btn-download">PDF</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($tugas->status) }}</td>
                    <td>
                        @if($tugas->status === 'menunggu')
                            <form action="{{ route('admin.konten.tugas.setujui', $tugas->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn btn-success btn-sm">Setujui</button>
                            </form>
                            <form action="{{ route('admin.konten.tugas.tolak', $tugas->id) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="btn btn-danger btn-sm">Tolak</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada tugas terbaru.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
