@extends('layouts.dosen')

@section('title', 'Penilaian Tugas')

@section('content')

<link href="{{ asset('css/backsite/dosen/penilaian.css') }}" rel="stylesheet">
<div class="container mt-4">
    <h4 class="mb-4 fw-bold d-flex align-items-center gap-2">
        <i class="fas fa-check-circle text-success"></i>
        <span>Penilaian Tugas: {{ $tugas->judul }} - Kelas {{ $tugas->kelas->nama_kelas }}</span>
    </h4>

    @if($pengumpul->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered shadow-sm align-middle text-center">
            <thead>
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>File Jawaban</th>
                    <th>Nilai</th>
                    <th>Feedback</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengumpul as $item)
                <tr>
                    <td>{{ $item->mahasiswa->name }}</td>
                    <td>
                        @if($item->file)
                            <a href="{{ asset('storage/' . $item->file) }}" target="_blank">
                                <i class="fas fa-file-alt me-1"></i> Lihat File
                            </a>
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('dosen.tugas_ujian.nilai', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}" method="POST" class="d-flex justify-content-center gap-2">
                            @csrf
                            <input type="hidden" name="mahasiswa_id" value="{{ $item->mahasiswa_id }}">
                            <input type="number" name="nilai" class="form-control form-control-sm text-center" min="0" max="100" value="{{ $item->nilai }}" placeholder="Nilai" style="width: 70px;">
                    </td>
                    <td>
                            <input type="text" name="feedback" class="form-control form-control-sm" value="{{ $item->feedback }}" placeholder="Feedback">
                    </td>
                    <td>
                            <button type="submit" class="btn btn-sm btn-simpan">Simpan</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="fas fa-user-times"></i>
            <span>Belum ada mahasiswa yang mengumpulkan tugas ini.</span>
        </div>
    @endif
</div>
@endsection