@extends('layouts.mahasiswa')
@section('title', 'Detail Tugas')

@section('content')
<h4 class="fw-bold">Detail Tugas</h4>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        {{ $tugas->judul }}
    </div>
    <div class="card-body">
        <p><strong>Kelas:</strong> {{ $kelas->nama_matakuliah }}</p>
        <p><strong>Deskripsi:</strong> {{ $tugas->deskripsi }}</p>
        <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y') }}</p>

        @if($sudahDikumpulkan)
            <p class="text-success">✅ Tugas sudah dikumpulkan</p>
        @else
        
            {{-- Form upload atau tombol tampilkan modal upload --}}
            <form action="{{ route('mahasiswa.kelas.tugas.upload', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file_tugas" class="form-label">Upload File Tugas</label>
                    <input type="file" name="file_tugas" id="file_tugas" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Kumpulkan</button>
            </form>
        @endif
    </div>
</div>
@endsection
