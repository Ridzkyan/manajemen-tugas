@extends('layouts.dosen')

@section('content')
<link href="{{ asset('css/backsite/dosen/tugas_ujian.css') }}" rel="stylesheet">

<div class="container py-4">
    {{-- Judul dan tombol --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-pencil-square me-2" style="color: #008080;"></i>
            Tugas & Ujian - Kelas {{ $kelas->nama_kelas }} ({{ $kelas->nama_matakuliah }})
        </h2>
        <button id="toggleFormBtn" type="button">
            <i class="bi bi-plus-circle me-1"></i> <span id="toggleText">Tampilkan Form</span>
        </button>
    </div>

    <div class="page-wrapper">
        {{-- Kolom kiri: Card --}}
        <div class="card-column">
            @forelse($tugas as $tgs)
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 180px;">
                        <div>
                            <h5 class="card-title fw-semibold mb-2 d-flex align-items-center" style="gap: 8px;">
                                <i class="bi bi-journal-text text-warning fs-5"></i> {{ $tgs->judul }}
                            </h5>

                            <span class="d-inline-block mb-2 px-3 py-1 bg-light text-dark rounded-pill fw-semibold text-uppercase" style="font-size: 0.75rem;">
                                {{ $tgs->tipe }}
                            </span>

                            @if($tgs->deadline)
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Deadline: {{ \Carbon\Carbon::parse($tgs->deadline)->translatedFormat('d F Y H:i') }}
                                </p>
                            @endif
                        </div>

                        <div class="d-flex justify-content-center mt-3 gap-3">
                            <a href="{{ route('dosen.kelola_kelas.edit', [$kelas->id, $tgs->id]) }}" class="btn btn-icon">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form action="{{ route('dosen.tugas_ujian.destroy', [$kelas->id, $tgs->id]) }}" method="POST" class="form-delete d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-delete btn-confirm-delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                            @if($tgs->file_soal)
                                <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank" class="btn btn-lihat-file">
                                    <i class="bi bi-file-earmark-text"></i> Lihat File
                                </a>
                            @endif

                            <a href="{{ route('dosen.tugas_ujian.penilaian', [$kelas->id, $tgs->id]) }}" class="btn-penilaian">
                                Penilaian
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">Belum ada tugas atau ujian yang ditambahkan.</div>
            @endforelse
        </div>

        {{-- Kolom kanan: Form upload --}}
        <div class="form-column" id="uploadForm">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white fw-semibold border-bottom d-flex align-items-center" style="gap: 8px;">
                    <i class="bi bi-upload fs-5" style="color: #008080;"></i> Upload Tugas / Ujian
                </div>
                <div class="card-body">
                    <form method="POST" id="formUploadTugas" action="{{ route('dosen.tugas_ujian.store', $kelas->id) }}" enctype="multipart/form-data">
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
                            <div id="fileUploadDropzone" class="dropzone border rounded p-4 text-center shadow-sm" style="min-height: 150px;">
                                <div class="dz-message">
                                    <i class="bi bi-cloud-arrow-up fs-3 text-muted mb-2 d-block"></i>
                                    <p class="mb-0 text-muted">Klik atau drag file untuk upload</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex align-items-center" style="gap: 6px;">
                                <i class="bi bi-calendar3 fs-5" style="color: #008080;"></i> Deadline
                            </label>
                            <input type="datetime-local" name="deadline" class="form-control shadow-sm">
                        </div>

                        <button type="button" id="btnSubmitForm" class="btn btn-upload w-100 mt-3">
                            <i class="bi bi-cloud-arrow-up me-2"></i> Upload Tugas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Toggle Script --}}
<script>
    document.getElementById('toggleFormBtn').addEventListener('click', function () {
        const form = document.getElementById('uploadForm');
        const text = document.getElementById('toggleText');
        form.classList.toggle('hide');
        text.textContent = form.classList.contains('hide') ? 'Tampilkan Form' : 'Sembunyikan Form';
    });
</script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".btn-confirm-delete");

    deleteButtons.forEach(button => {
        button.addEventListener("click", function () {
            Swal.fire({
                title: 'Yakin?',
                text: "Tugas akan dihapus dan tidak bisa dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest("form").submit();
                }
            });
        });
    });
});
</script>

<!-- Dropzone CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<script>
Dropzone.autoDiscover = false;

const myForm = document.getElementById("formUploadTugas");
const btnSubmit = document.getElementById("btnSubmitForm");

const dz = new Dropzone("#fileUploadDropzone", {
    url: myForm.action,
    autoProcessQueue: false,
    uploadMultiple: false,
    maxFiles: 1,
    maxFilesize: 5,
    acceptedFiles: ".pdf,.doc,.docx",
    paramName: "file_soal",
    previewsContainer: "#fileUploadDropzone",
    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
    init: function () {
        const dzInstance = this;

        btnSubmit.addEventListener("click", function () {
            if (dzInstance.getAcceptedFiles().length === 0) {
                Swal.fire("Oops", "Silakan upload file terlebih dahulu!", "warning");
            } else {
                dzInstance.processQueue();
            }
        });

        dzInstance.on("sending", function(file, xhr, formData) {
            const inputs = myForm.querySelectorAll("input, textarea, select");
            inputs.forEach(input => {
                if (input.name && input.type !== "file") {
                    formData.append(input.name, input.value);
                }
            });
        });

        dzInstance.on("success", function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Tugas berhasil diupload!',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            setTimeout(() => {
                window.location.reload();
            }, 2000);
        });

        dzInstance.on("error", function (file, response) {
            Swal.fire("Gagal", response.message || "Upload gagal", "error");
        });
    }
});
</script>
@endsection 