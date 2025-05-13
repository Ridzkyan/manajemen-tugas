@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Tugas & Ujian - Kelas {{ $kelas->nama_kelas }} ({{ $kelas->nama_matakuliah }})</h4>
        <button class="btn btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#uploadForm">+ Tambah Tugas</button>
    </div>

    {{-- Form Upload Tugas --}}
    <div id="uploadForm" class="collapse mb-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Upload Tugas / Ujian</div>
            <div class="card-body">
                <form method="POST" action="{{ route('dosen.tugas_ujian.store', $kelas->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="tipe" class="form-select" required>
                            <option value="tugas">Tugas</option>
                            <option value="ujian">Ujian</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload File Soal</label>
                        <div class="dropzone border rounded p-3 text-center" id="fileUploadDropzone">Drop files here to upload</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Upload Tugas</button>
                </form>
            </div>
        </div>
    </div>

    {{-- List Tugas --}}
    <div class="row">
        @forelse($tugas as $tgs)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <h5 class="card-title mb-2">{{ $tgs->judul }}</h5>
                    <span class="badge bg-primary text-uppercase">{{ $tgs->tipe }}</span>

                    @if($tgs->file_soal)
                        <div class="mt-2">📎 <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank">Lihat File</a></div>
                    @endif

                    @if($tgs->deadline)
                        <div class="text-muted mt-1">🗓 Deadline: {{ \Carbon\Carbon::parse($tgs->deadline)->translatedFormat('d F Y') }}</div>
                    @endif

                    <hr>

                    @if(!$tgs->nilai)
                        <a href="{{ route('dosen.tugas_ujian.penilaian', ['kelas' => $kelas->id, 'tugas' => $tgs->id]) }}" class="btn btn-sm btn-outline-warning w-100">Penilaian</a>
                    @else
                        <div class="text-success mb-1 fw-semibold">✅ Nilai: {{ $tgs->nilai }}</div>
                        <small class="text-muted">{{ $tgs->feedback }}</small>
                    @endif
                </div>
            </div>
        </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Belum ada tugas atau ujian yang ditambahkan.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- Dropzone --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<script>
Dropzone.autoDiscover = false;

const dz = new Dropzone("#fileUploadDropzone", {
    url: "{{ route('dosen.tugas_ujian.store', $kelas->id) }}",
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
