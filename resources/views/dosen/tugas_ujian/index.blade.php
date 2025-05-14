@extends('layouts.dosen')

@section('content')
<style>
    .btn-icon {
        background-color: transparent;
        border: none;
        font-size: 1.2rem;
        color: #008080;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .btn-icon:hover {
        color: #f5a04e;
        transform: scale(1.1);
    }

    .btn-delete {
        background-color: transparent;
        border: none;
        font-size: 1.2rem;
        color: #dc3545;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .btn-delete:hover {
        color: #f5a04e;
        transform: scale(1.1);
    }

    .badge-taskflow {
        background-color: #008080;
        color: white;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.35em 0.6em;
        border-radius: 0.4rem;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1rem;
        height: 100%;
        position: relative;
    }

    .card {
        border-radius: 12px;
        position: relative;
        padding: 1rem;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px; /* Menambahkan jarak antar card */
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .card-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px; /* Jarak antar tombol */
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .card-actions button {
        font-size: 1.2rem;
        padding: 0.5rem;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .card-actions button:hover {
        transform: scale(1.1);
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start;
    }

    .card-container .card {
        width: 300px;
        margin-bottom: 20px;
    }

    .card-body p {
        margin: 0;
        font-size: 0.9rem;
    }

    .card-body .badge-taskflow {
        font-size: 0.85rem;
        padding: 0.3em 0.5em;
    }

    .card-container .card + .card {
        margin-top: 20px;
    }
</style>

<div class="container py-4">
    {{-- Judul dan tombol --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-pencil-square me-2" style="color: #008080;"></i>
            Tugas & Ujian - Kelas {{ $kelas->nama_kelas }} ({{ $kelas->nama_matakuliah }})
        </h2>
        <button class="btn btn-orange" type="button" data-bs-toggle="collapse" data-bs-target="#uploadForm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Tugas
        </button>
    </div>

    {{-- Form Upload --}}
    <div id="uploadForm" class="collapse mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white fw-semibold border-bottom d-flex align-items-center" style="gap: 8px;">
                <i class="bi bi-upload fs-5" style="color: #008080;"></i> Upload Tugas / Ujian
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dosen.tugas_ujian.store', $kelas->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex align-items-center" style="gap: 6px;">
                            <i class="bi bi-type fs-5" style="color: #008080;"></i> Judul
                        </label>
                        <input type="text" name="judul" class="form-control shadow-sm" placeholder="Masukkan judul tugas..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex align-items-center" style="gap: 6px;">
                            <i class="bi bi-ui-checks-grid fs-5" style="color: #008080;"></i> Tipe
                        </label>
                        <select name="tipe" class="form-select shadow-sm" required>
                            <option value="tugas">Tugas</option>
                            <option value="ujian">Ujian</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex align-items-center" style="gap: 6px;">
                            <i class="bi bi-textarea-resize fs-5" style="color: #008080;"></i> Deskripsi
                        </label>
                        <textarea name="deskripsi" class="form-control shadow-sm" rows="3" placeholder="Tulis deskripsi tugas di sini..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex align-items-center" style="gap: 6px;">
                            <i class="bi bi-file-earmark-arrow-up fs-5" style="color: #008080;"></i> Upload File Soal
                        </label>
                        <div class="dropzone border rounded p-4 text-center shadow-sm" id="fileUploadDropzone" style="min-height: 120px;">
                            <i class="bi bi-cloud-arrow-up fs-3 text-muted mb-2"></i>
                            <p class="mb-0 text-muted">Klik atau drag file untuk upload</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold d-flex align-items-center" style="gap: 6px;">
                            <i class="bi bi-calendar3 fs-5" style="color: #008080;"></i> Deadline
                        </label>
                        <input type="date" name="deadline" class="form-control shadow-sm">
                    </div>

                    <button type="submit" class="btn btn-orange w-100 mt-2">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload Tugas
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- List Tugas --}}
    <div class="card-container">
        @forelse($tugas as $tgs)
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center" style="gap: 8px;">
                            <i class="bi bi-journal-text text-warning fs-5"></i> {{ $tgs->judul }}
                        </h5>

                        <span class="badge-taskflow mb-3">{{ strtoupper($tgs->tipe) }}</span>

                        @if($tgs->deadline)
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                <i class="bi bi-calendar-event me-1"></i>
                                Deadline: {{ \Carbon\Carbon::parse($tgs->deadline)->translatedFormat('d F Y') }}
                            </p>
                        @endif

                        @if($tgs->file_soal)
                            <p class="mb-0" style="font-size: 0.9rem;">
                                📎 <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank">Lihat File</a>
                            </p>
                        @endif
                    </div>

                    {{-- Tombol Edit, Hapus, dan Penilaian --}}
                    <div class="card-actions">
                        <a href="{{ route('dosen.tugas_ujian.edit', [$kelas->id, $tgs->id]) }}" class="btn btn-icon">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('dosen.tugas_ujian.destroy', [$kelas->id, $tgs->id]) }}" method="POST" class="d-inline mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        {{-- Penilaian Button --}}
                        <a href="{{ route('dosen.tugas_ujian.penilaian', [$kelas->id, $tgs->id]) }}" class="btn btn-icon">
                            <i class="bi bi-star"></i>
                        </a>
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

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
</script>
@endif

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
                    window.location.href = window.location.href + '?success=1';
                } else {
                    alert("Gagal upload!");
                }
            });
        });
    }
});
</script>
@endsection