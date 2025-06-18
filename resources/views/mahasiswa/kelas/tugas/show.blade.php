@extends('layouts.mahasiswa')
@section('title', 'Detail Tugas')

@section('content')

{{-- Import CSS khusus halaman detail tugas --}}
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/tugas_show.css') }}">

<div class="detail-wrapper">
    <div class="card card-detail mb-4">
        <div class="card-detail-header">
            {{ $tugas->judul }}
        </div>
        <div class="card-detail-body">
            <p><strong>Kelas:</strong> {{ $kelas->nama_matakuliah }}</p>
            <p><strong>Deskripsi:</strong> {{ $tugas->deskripsi }}</p>
            <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y H:i') }}</p>

            @php
                $deadline = \Carbon\Carbon::parse($tugas->deadline);
                $now = \Carbon\Carbon::now();
                $isDeadlineOver = $now->gt($deadline);
            @endphp

            {{-- Tampilkan pesan error atau success --}}
            @if(session('tugas_error'))
                <div class="alert alert-danger">
                    {{ session('tugas_error') }}
                </div>
            @endif

            @if(session('tugas_success'))
                <div class="alert alert-success">
                    {{ session('tugas_success') }}
                </div>
            @endif

            {{-- Tampilkan validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($tugas->file_soal)
                <p><strong>File Soal:</strong></p>
                <p>{{ basename($tugas->file_soal) }}</p>
                <a href="{{ asset('storage/' . $tugas->file_soal) }}" class="btn btn-sm btn-primary mt-2" target="_blank">
                    <i class="fas fa-download"></i> Download Soal
                </a>
            @else
                <p class="text-muted">Tidak ada file soal.</p>
            @endif

            <hr>

            @if($sudahDikumpulkan)
                <div class="alert alert-success">
                    <p class="mb-2"><strong>Status:</strong> Tugas sudah dikumpulkan</p>
                    <p class="mb-0"><strong>Waktu pengumpulan:</strong> {{ $pengumpulan->created_at ? $pengumpulan->created_at->format('d M Y H:i') : 'Tidak diketahui' }}</p>
                </div>

                @if($pengumpulan && $pengumpulan->file_jawaban)
                    <p><strong>File Jawaban:</strong></p>
                    <p>{{ basename($pengumpulan->file_jawaban) }}</p>
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $pengumpulan->file_jawaban) }}" class="btn btn-sm btn-success me-2" target="_blank">
                            <i class="fas fa-download"></i> Download Jawaban
                        </a>
                        
                        {{-- Tombol hapus jika deadline belum lewat --}}
                        @if(!$isDeadlineOver)
                            <form action="{{ route('mahasiswa.kelas.tugas.delete', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file tugas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    <p class="text-danger">File jawaban tidak ditemukan.</p>
                @endif
            @elseif($isDeadlineOver)
                <div class="alert alert-warning">
                    <p class="mb-0"><strong>Deadline sudah lewat.</strong> Anda tidak bisa mengumpulkan tugas ini.</p>
                </div>
            @else
                <div class="alert alert-info">
                    <p class="mb-0">Silakan upload file jawaban Anda sebelum deadline.</p>
                </div>

                <form action="{{ route('mahasiswa.kelas.tugas.upload', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      id="uploadForm">
                    @csrf
                    <div class="mb-3">
                        <label for="file_tugas" class="form-label">
                            <strong>Upload File Jawaban</strong>
                        </label>
                        <input type="file" 
                               name="file_tugas" 
                               id="file_tugas" 
                               class="form-control @error('file_tugas') is-invalid @enderror" 
                               accept=".pdf,.doc,.docx,.txt" 
                               required>
                        <div class="form-text">
                            Format yang diperbolehkan: PDF, DOC, DOCX, TXT. Maksimal 10MB.
                        </div>
                        @error('file_tugas')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-upload"></i> Kumpulkan Tugas
                    </button>
                </form>

                {{-- JavaScript untuk loading state --}}
                <script>
                document.getElementById('uploadForm').addEventListener('submit', function(e) {
                    const submitBtn = document.getElementById('submitBtn');
                    const fileInput = document.getElementById('file_tugas');
                    
                    // Validasi file dipilih
                    if (!fileInput.files.length) {
                        e.preventDefault();
                        alert('Pilih file terlebih dahulu!');
                        return;
                    }
                    
                    // Validasi ukuran file (10MB = 10485760 bytes)
                    if (fileInput.files[0].size > 10485760) {
                        e.preventDefault();
                        alert('Ukuran file terlalu besar! Maksimal 10MB.');
                        return;
                    }
                    
                    // Tampilkan loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';
                    submitBtn.disabled = true;
                });

                // Preview file yang dipilih
                document.getElementById('file_tugas').addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        console.log('File dipilih:', file.name, 'Ukuran:', file.size, 'bytes');
                    }
                });
                </script>
            @endif
        </div>
    </div>
</div>
@endsection