@extends('layouts.dosen')

@section('title', 'Penilaian Tugas')

@section('content')

<link href="{{ asset('css/backsite/dosen/penilaian.css') }}" rel="stylesheet">
<div class="container mt-4">
    <h4 class="mb-4 fw-bold d-flex align-items-center gap-2">
        <i class="fas fa-check-circle text-success"></i>
        <span>Penilaian Tugas: {{ $tugas->judul }} - Kelas {{ $tugas->kelas->nama_kelas }}</span>
    </h4>

   <div class="mb-3 d-flex justify-content-end">
    <form action="{{ route('dosen.tugas_ujian.nilai.batch', [$kelas->id, $tugas->id]) }}" method="POST" 
          onsubmit="return confirm('Yakin ingin menilai otomatis semua jawaban mahasiswa?')">
        @csrf
        <button type="submit" class="btn btn-success btn-sm">
            <i class="bi bi-lightning-fill me-1"></i> Nilai Otomatis Semua
        </button>
    </form>
</div>

    @if($pengumpul->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered shadow-sm align-middle text-center">
            <thead class="table-light">
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
                        @if($item->file_jawaban)
                            <a href="{{ asset('storage/' . $item->file_jawaban) }}" target="_blank">
                                <i class="fas fa-file-alt me-1"></i> Lihat File
                            </a>
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </td>
                    <td>
                        {{-- PERBAIKAN: Ubah route name dari tugas_ujian.nilai ke tugas_ujian.nilai --}}
                        <form action="{{ route('dosen.tugas_ujian.penilaian', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}"
                              method="POST"
                              class="d-flex justify-content-center gap-2">
                            @csrf
                            {{-- PERBAIKAN: Tambahkan hidden input untuk ID pengumpul --}}
                            <input type="hidden" name="pengumpul_id" value="{{ $item->id }}">
                            <input type="number"
                                name="nilai"
                                class="form-control form-control-sm text-center"
                                min="0" max="100"
                                value="{{ $item->nilai ? round($item->nilai, 2) : '' }}"
                                placeholder="Nilai"
                                style="width: 70px;">
                    </td>
                    <td>
                        <input type="text"
                               name="feedback"
                               class="form-control form-control-sm"
                               value="{{ $item->feedback }}"
                               placeholder="Feedback otomatis/manual">
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

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '{{ session('error') }}',
        showConfirmButton: true
    });
</script>
@endif

@endsection