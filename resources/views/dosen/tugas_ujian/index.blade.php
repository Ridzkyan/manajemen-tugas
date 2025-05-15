@extends('layouts.dosen')

@section('content')
<style>
    html, body {
        height: 100%;
    }

    #toggleFormBtn {
        background-color: #f5a04e;
        color: white;
        border: none;
        padding: 6px 14px;
        font-size: 0.9rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    #toggleFormBtn:hover {
        background-color: #008080;
        color: white;
    }

    /* Penilaian Button */
    .btn-penilaian {
        background-color: #008080;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-penilaian:hover {
        background-color: #f5a04e;
        text-decoration: none;
    }

    /* Edit & Delete Icon Buttons */
    .btn-icon, .btn-delete {
        background-color: #f0f0f0;
        border: none;
        font-size: 1.1rem;
        border-radius: 6px;
        padding: 6px 10px;
        transition: all 0.2s ease;
    }

    .btn-icon {
        color: #008080;
    }

    .btn-icon:hover {
        background-color: #f5a04e;
        color: white;
        transform: scale(1.1);
    }

    .btn-delete {
        color: #dc3545;
    }

    .btn-delete:hover {
        background-color: #f5a04e;
        color: white;
        transform: scale(1.1);
    }

    .btn-upload {
        background-color: #008080;
        color: white;
        border: none;
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(0, 128, 128, 0.2);
    }

    .btn-upload:hover {
        background-color: #f5a04e;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(245, 160, 78, 0.3);
    }

    .badge-taskflow {
        background-color: #008080;
        color: white;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.35em 0.6em;
        border-radius: 0.4rem;
    }

    .page-wrapper {
        display: flex;
        gap: 24px;
        align-items: flex-start;
        min-height: calc(100vh - 100px);
        transition: all 0.3s ease;
    }

    .card-column {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
        transition: all 0.3s ease;
    }

    .form-column {
        width: 100%;
        max-width: 600px;
        transition: all 0.3s ease;
    }

    .card {
        border-radius: 12px;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1rem;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .card-actions {
        display: flex;
        justify-content: flex-start;
        gap: 12px;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .hide {
        display: none !important;
    }
</style>

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
                                    Deadline: {{ \Carbon\Carbon::parse($tgs->deadline)->translatedFormat('d F Y') }}
                                </p>
                            @endif

                            @if($tgs->file_soal)
                                <p class="mb-0">
                                    📎 <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank">Lihat File</a>
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
                            <input type="date" name="deadline" class="form-control shadow-sm">
                        </div>

                        <button type="submit" class="btn btn-upload w-100 mt-3">
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
    document.addEventListener("DOMContentLoaded", function () {
        const deleteButtons = document.querySelectorAll('.btn-confirm-delete');

        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function () {
                const form = this.closest('form');

                Swal.fire({
                    title: 'Yakin ingin menghapus tugas?',
                    text: "Data ini tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#008080',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
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