@extends('layouts.dosen')

@section('content')
<div class="container">
    <h4 class="mb-4">Penilaian Tugas: {{ $tugas->judul }} - Kelas {{ $tugas->kelas->nama_kelas }}</h4>

    @if($pengumpul->count() > 0)
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nama Mahasiswa</th>
                <th>File Jawaban</th>
                <th>Nilai</th>
                <th>Feedback</th>
                <th>Penilaian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengumpul as $item)
            <tr>
                <td>{{ $item->mahasiswa->name }}</td>
                <td>
                    @if($item->file)
                        <a href="{{ asset('storage/' . $item->file) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
                <td>
                    <form action="{{ route('dosen.tugas_ujian.nilai', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}" method="POST" class="d-flex gap-2 align-items-center">
                        @csrf
                        <input type="hidden" name="mahasiswa_id" value="{{ $item->mahasiswa_id }}">
                        <input type="number" name="nilai" class="form-control form-control-sm" min="0" max="100" value="{{ $item->nilai }}" placeholder="Nilai" style="width: 80px;">
                </td>
                <td>
                        <input type="text" name="feedback" class="form-control form-control-sm" value="{{ $item->feedback }}" placeholder="Feedback">
                </td>
                <td>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="alert alert-info">Belum ada mahasiswa yang mengumpulkan tugas ini.</div>
    @endif
</div>
@endsection
