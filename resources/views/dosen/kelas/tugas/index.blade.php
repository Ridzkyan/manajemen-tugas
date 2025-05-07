@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tugas/Ujian untuk Kelas: {{ $kelas->nama_kelas }}</h3>

    {{-- Alert Success/Error --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form Upload Tugas --}}
    <div class="card mb-4">
        <div class="card-header">
            Upload Tugas / Ujian
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dosen.tugas.store', $kelas->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Tipe</label>
                    <select name="tipe" class="form-control" required>
                        <option value="tugas">Tugas</option>
                        <option value="ujian">Ujian</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label>Upload File Soal</label>
                    <input type="file" name="file_soal" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Deadline</label>
                    <input type="date" name="deadline" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Upload Tugas</button>
            </form>
        </div>
    </div>

    {{-- List Tugas --}}
    <div class="card">
        <div class="card-header">
            Daftar Tugas/Ujian
        </div>
        <div class="card-body">
            @if($tugas->count() > 0)
                <ul>
                    @foreach($tugas as $tgs)
                        <li>
                            {{ $tgs->judul }} ({{ ucfirst($tgs->tipe) }}) 

                            {{-- Menampilkan tombol penilaian --}}
                            @if(!$tgs->nilai)  {{-- Cek jika tugas belum dinilai --}}
                                <a href="{{ route('dosen.tugas.penilaian', ['kelas' => $kelas->id, 'tugas' => $tgs->id]) }}" class="btn btn-warning btn-sm">Penilaian</a>
                            @else
                                {{-- Menampilkan nilai dan feedback jika sudah dinilai --}}
                                <span class="badge bg-success">Nilai: {{ $tgs->nilai }}</span>
                                <p><strong>Feedback:</strong> {{ $tgs->feedback }}</p>
                            @endif

                            @if($tgs->file_soal)
                                <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank">Download Soal</a>
                            @endif
                            @if($tgs->deadline)
                                - Deadline: {{ $tgs->deadline }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Belum ada tugas yang diupload.</p>
            @endif
        </div>
    </div>
    <a href="{{ route('dosen.kelas.index') }}" class="btn btn-secondary">Kembali ke Daftar Kelas</a>
</div>
@endsection
