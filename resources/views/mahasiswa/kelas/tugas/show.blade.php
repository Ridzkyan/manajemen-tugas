@extends('layouts.mahasiswa')
@section('title', 'Detail Tugas')

@section('content')
<style>
    .detail-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }

    .card-detail {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        overflow: hidden;
        border: none;
    }

    .card-detail-header {
        background-color: #008080; /* Toska */
        color: white;
        font-weight: 600;
        font-size: 1.2rem;
        padding: 16px 24px;
    }

    .card-detail-body {
        padding: 24px;
        color: #333;
    }

    .card-detail-body p {
        margin-bottom: 12px;
    }

    .upload-label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    .btn-kumpul {
        background-color: #f5a04e;
        color: white;
        font-weight: 600;
        padding: 10px 24px;
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-kumpul:hover {
        background-color: #d88c2f;
        transform: translateY(-1px);
    }

    .status-sukses {
        font-weight: bold;
        color: #28a745;
        margin-top: 10px;
    }
</style>

<div class="container py-3">
    <h3 class="mb-4 text-center">Detail Tugas</h3>

<div class="detail-wrapper">
    <div class="card card-detail mb-4">
        <div class="card-detail-header">
            {{ $tugas->judul }}
        </div>
        <div class="card-detail-body">
            <p><strong>Kelas:</strong> {{ $kelas->nama_matakuliah }}</p>
            <p><strong>Deskripsi:</strong> {{ $tugas->deskripsi }}</p>
            <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') }}</p>

            @if($sudahDikumpulkan)
                <p class="status-sukses">✅ Tugas sudah dikumpulkan</p>
            @else
                <form action="{{ route('mahasiswa.kelas.tugas.upload', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file_tugas" class="upload-label">Upload File Tugas</label>
                        <input type="file" name="file_tugas" id="file_tugas" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-kumpul">
                        <i class="fas fa-upload"></i> Kumpulkan
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>


@endsection
