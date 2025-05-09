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
                    <div class="dropzone" id="fileUploadDropzone"></div>
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

                            @if(!$tgs->nilai)
                                <a href="{{ route('dosen.tugas.penilaian', ['kelas' => $kelas->id, 'tugas' => $tgs->id]) }}" class="btn btn-warning btn-sm">Penilaian</a>
                            @else
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

<!-- Dropzone CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<script>
    Dropzone.autoDiscover = false;

    const dz = new Dropzone("#fileUploadDropzone", {
        url: "{{ route('dosen.tugas.store', $kelas->id) }}",
        autoProcessQueue: false,
        uploadMultiple: false,
        maxFiles: 1,
        maxFilesize: 5,
        acceptedFiles: '.pdf,.doc,.docx',
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        paramName: 'file_soal',
        init: function () {
            const myDropzone = this;
            document.querySelector("form").addEventListener("submit", function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                if (myDropzone.getAcceptedFiles().length > 0) {
                    formData.append('file_soal', myDropzone.getAcceptedFiles()[0]);
                }

                fetch(this.action, {
                    method: "POST",
                    body: formData,
                }).then(response => {
                    if (response.ok) {
                        alert("Tugas berhasil diupload!");
                        window.location.reload();
                    } else {
                        alert("Gagal upload!");
                    }
                });
            });
        }
    });
</script>
@endsection
