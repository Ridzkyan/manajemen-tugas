@extends('layouts.mahasiswa')
@section('title', 'Kerjakan Ujian')

@section('content')
<h4 class="fw-bold">Kerjakan Ujian</h4>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        {{ $ujian->judul }}
    </div>
    <div class="card-body">
        <p><strong>Kelas:</strong> {{ $ujian->kelas->nama_matakuliah ?? '-' }}</p>
        <p><strong>Deskripsi:</strong> {{ $ujian->deskripsi }}</p>
        <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($ujian->deadline)->format('d M Y') }}</p>

        @if($sudahDikumpulkan ?? false)
            <p class="text-success">✅ Ujian sudah dikumpulkan</p>
        @else
            <form action="{{ route('mahasiswa.kelas.ujian.kumpul', ['kelasId' => $kelasId, 'id' => $ujian->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="jawaban" class="form-label">Jawaban Ujian</label>
                    <textarea name="jawaban" id="jawaban" rows="8" class="form-control" placeholder="Tulis jawabanmu di sini..." required></textarea>
                </div>
                <div class="mb-3">
                    <label for="file_ujian" class="form-label">Upload File (Opsional)</label>
                    <input type="file" name="file_ujian" id="file_ujian" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Kumpulkan</button>
            </form>
        @endif
    </div>
</div>
@endsection
