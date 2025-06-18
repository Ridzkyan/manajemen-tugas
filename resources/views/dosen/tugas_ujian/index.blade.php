@extends('layouts.dosen')

@section('content')
<link href="{{ asset('css/backsite/dosen/tugas_ujian.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-pencil-square me-2" style="color: #008080;"></i>
            Tugas & Ujian - Kelas {{ $kelas->nama_kelas }} ({{ $kelas->nama_matakuliah }})
        </h2>
        <div class="d-flex gap-2">
            <button id="toggleFormBtn" type="button">
                <i class="bi bi-plus-circle me-1"></i> <span id="toggleText">Tampilkan Form</span>
            </button>
            <button id="bulkDeleteBtn" type="button" class="btn btn-danger btn-sm" style="display: none;">
                <i class="bi bi-trash me-1"></i> Hapus Terpilih
            </button>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="card-column">
            <!-- Bulk select checkbox -->
            <div class="mb-3" id="bulkSelectContainer" style="display: none;">
                <label>
                    <input type="checkbox" id="selectAll" class="me-2">
                    Pilih Semua
                </label>
            </div>

            @forelse($tugas as $tgs)
                <div class="card shadow-sm border-0" data-tugas-id="{{ $tgs->id }}">
                    <div class="card-body d-flex flex-column justify-content-between" style="min-height: 180px;">
                        <div>
                            <!-- Checkbox untuk bulk select -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="bulk-select-checkbox" style="display: none;">
                                    <input type="checkbox" class="tugas-checkbox" value="{{ $tgs->id }}">
                                </div>
                                <h5 class="card-title fw-semibold mb-2 d-flex align-items-center flex-grow-1" style="gap: 8px;">
                                    <i class="bi bi-journal-text text-warning fs-5"></i> {{ $tgs->judul }}
                                </h5>
                            </div>
                            <span class="d-inline-block mb-2 px-3 py-1 bg-light text-dark rounded-pill fw-semibold text-uppercase" style="font-size: 0.75rem;">
                                {{ $tgs->tipe }}
                            </span>
                            @if($tgs->deadline)
                                <p class="text-muted mb-2">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Deadline: {{ \Carbon\Carbon::parse($tgs->deadline)->translatedFormat('d F Y H:i') }}
                                </p>
                            @endif
                            <!-- Status soft delete -->
                            @if($tgs->deleted_at)
                                <span class="badge bg-secondary mb-2">
                                    <i class="bi bi-archive me-1"></i>Diarsipkan
                                </span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-center mt-3 gap-2 flex-wrap">
                            @if(!$tgs->deleted_at)
                                <!-- Tombol hapus dengan dropdown options -->
                                <div class="dropdown">
                                    <button class="btn btn-delete dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item btn-delete-info" data-tugas-id="{{ $tgs->id }}">
                                                <i class="bi bi-info-circle me-2"></i>Info Hapus
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item btn-soft-delete" data-tugas-id="{{ $tgs->id }}">
                                                <i class="bi bi-archive me-2"></i>Arsipkan
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger btn-permanent-delete" data-tugas-id="{{ $tgs->id }}">
                                                <i class="bi bi-trash me-2"></i>Hapus Permanen
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                
                               
                            @else
                                <!-- Restore button untuk tugas yang di-soft delete -->
                                <button class="btn btn-success btn-sm btn-restore" data-tugas-id="{{ $tgs->id }}">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Pulihkan
                                </button>
                            @endif
                            
                            @if($tgs->file_soal)
                                <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank" class="btn btn-lihat-file btn-sm">
                                    <i class="bi bi-file-earmark-text"></i> Lihat File
                                </a>
                                <!-- Hapus file soal -->
                                <button class="btn btn-outline-danger btn-sm btn-delete-file" data-tugas-id="{{ $tgs->id }}" data-file-type="soal">
                                    <i class="bi bi-file-x"></i>
                                </button>
                            @endif
                            
                            @if($tgs->file_kunci)
                                <a href="{{ asset('storage/' . $tgs->file_kunci) }}" target="_blank" class="btn btn-info btn-sm">
                                    <i class="bi bi-key"></i> Kunci
                                </a>
                                <!-- Hapus file kunci -->
                                <button class="btn btn-outline-danger btn-sm btn-delete-file" data-tugas-id="{{ $tgs->id }}" data-file-type="kunci">
                                    <i class="bi bi-key-fill"></i><i class="bi bi-x"></i>
                                </button>
                            @endif
                            
                            <a href="{{ route('dosen.tugas_ujian.nilai', [$kelas->id, $tgs->id]) }}" class="btn-penilaian btn-sm">
                                Penilaian
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">Belum ada tugas atau ujian yang ditambahkan.</div>
            @endforelse
        </div>

        <div class="form-column" id="uploadForm">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white fw-semibold border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <i class="bi bi-upload fs-5" style="color: #008080;"></i> Upload Tugas / Ujian
                    </div>
                    <button type="button" id="toggleBulkSelect" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-check-square me-1"></i>Mode Pilih
                    </button>
                </div>
                <div class="card-body">
                    <form method="POST" id="formUploadTugas" action="{{ route('dosen.tugas_ujian.store', $kelas->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul</label>
                            <input type="text" name="judul" class="form-control shadow-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe</label>
                            <select name="tipe" class="form-select shadow-sm" required>
                                <option value="tugas">Tugas</option>
                                <option value="ujian">Ujian</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control shadow-sm" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload File Soal (.docx)</label>
                            <div id="fileUploadDropzone" class="dropzone border rounded p-4 text-center shadow-sm">
                                <div class="dz-message">
                                    <i class="bi bi-cloud-arrow-up fs-3 text-muted mb-2 d-block"></i>
                                    <p class="mb-0 text-muted">Klik atau drag file .docx ke sini</p>
                                    <small class="text-muted">File maksimal 5MB</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Kunci Jawaban (.docx)</label>
                            <input type="file" name="file_kunci" accept=".docx" class="form-control shadow-sm">
                            <small class="text-muted">File maksimal 5MB (Opsional)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deadline</label>
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

<script>
document.getElementById('toggleFormBtn').addEventListener('click', function () {
    const form = document.getElementById('uploadForm');
    const text = document.getElementById('toggleText');
    form.classList.toggle('hide');
    text.textContent = form.classList.contains('hide') ? 'Tampilkan Form' : 'Sembunyikan Form';
});

// Bulk select functionality
document.getElementById('toggleBulkSelect').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.bulk-select-checkbox');
    const bulkContainer = document.getElementById('bulkSelectContainer');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    checkboxes.forEach(cb => {
        cb.style.display = cb.style.display === 'none' ? 'block' : 'none';
    });
    
    bulkContainer.style.display = bulkContainer.style.display === 'none' ? 'block' : 'none';
    bulkDeleteBtn.style.display = bulkDeleteBtn.style.display === 'none' ? 'inline-block' : 'none';
    
    // Reset checkboxes
    document.querySelectorAll('.tugas-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
});

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.tugas-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Enhanced delete operations
document.addEventListener('click', function(e) {
    // Delete info
    if (e.target.closest('.btn-delete-info')) {
        const tugasId = e.target.closest('.btn-delete-info').dataset.tugasId;
        
        fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/${tugasId}/delete-info`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Info Hapus Tugas',
                        html: `
                            <div class="text-start">
                                <p><strong>Judul:</strong> ${data.judul}</p>
                                <p><strong>Tipe:</strong> ${data.tipe}</p>
                                <p><strong>Jumlah Pengumpulan:</strong> ${data.pengumpulan_count}</p>
                                ${data.pengumpulan_count > 0 ? 
                                    '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Tugas ini sudah memiliki pengumpulan!</div>' : 
                                    '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Tugas ini belum memiliki pengumpulan.</div>'
                                }
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal mengambil informasi tugas', 'error');
            });
    }
    
    // Soft delete
    if (e.target.closest('.btn-soft-delete')) {
        const tugasId = e.target.closest('.btn-soft-delete').dataset.tugasId;
        
        Swal.fire({
            title: 'Arsipkan Tugas?',
            text: 'Tugas akan diarsipkan dan dapat dipulihkan kembali.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Arsipkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/${tugasId}/soft-delete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', 'Tugas berhasil diarsipkan.', 'success');
                        location.reload();
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal mengarsipkan tugas', 'error');
                });
            }
        });
    }
    
    // Permanent delete
    if (e.target.closest('.btn-permanent-delete')) {
        const tugasId = e.target.closest('.btn-permanent-delete').dataset.tugasId;
        
        Swal.fire({
            title: 'Hapus Permanen?',
            text: 'Tugas akan dihapus secara permanen dan tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus Permanen',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                // Check if has submissions first
                fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/${tugasId}/delete-info`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.pengumpulan_count > 0) {
                            // Confirm delete with submissions
                            fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/${tugasId}/confirm-delete`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    Swal.fire('Berhasil!', 'Tugas berhasil dihapus.', 'success');
                                    location.reload();
                                } else {
                                    Swal.fire('Gagal!', result.message, 'error');
                                }
                            });
                        } else {
                            // Regular delete
                            fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/${tugasId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    Swal.fire('Berhasil!', 'Tugas berhasil dihapus.', 'success');
                                    location.reload();
                                } else {
                                    Swal.fire('Gagal!', result.message, 'error');
                                }
                            });
                        }
                    });
            }
        });
    }
    
    // Restore
    if (e.target.closest('.btn-restore')) {
        const tugasId = e.target.closest('.btn-restore').dataset.tugasId;
        
        Swal.fire({
            title: 'Pulihkan Tugas?',
            text: 'Tugas akan dipulihkan dari arsip.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Pulihkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/${tugasId}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', 'Tugas berhasil dipulihkan.', 'success');
                        location.reload();
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memulihkan tugas', 'error');
                });
            }
        });
    }
    
    // Delete specific file
    if (e.target.closest('.btn-delete-file')) {
        const tugasId = e.target.closest('.btn-delete-file').dataset.tugasId;
        const fileType = e.target.closest('.btn-delete-file').dataset.fileType;
        
        Swal.fire({
            title: `Hapus File ${fileType === 'soal' ? 'Soal' : 'Kunci Jawaban'}?`,
            text: 'File akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/${tugasId}/file?type=${fileType}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', `File ${fileType} berhasil dihapus.`, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal menghapus file', 'error');
                });
            }
        });
    }
});

// Bulk delete
document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.tugas-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        Swal.fire('Peringatan', 'Pilih tugas yang ingin dihapus terlebih dahulu.', 'warning');
        return;
    }
    
    const tugasIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    Swal.fire({
        title: 'Hapus Tugas Terpilih?',
        text: `${tugasIds.length} tugas akan dihapus secara permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/dosen/kelas/{{ $kelas->id }}/tugas-ujian/bulk-delete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ tugas_ids: tugasIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', `${data.deleted_count} tugas berhasil dihapus.`, 'success');
                    location.reload();
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal menghapus tugas', 'error');
            });
        }
    });
});

// PERBAIKAN: Konfigurasi Dropzone yang lebih robust
Dropzone.autoDiscover = false;
const myForm = document.getElementById("formUploadTugas");
const btnSubmit = document.getElementById("btnSubmitForm");

// Fungsi untuk validasi file DOCX
function validateDocxFile(file) {
    const validExtensions = ['docx'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    
    if (!validExtensions.includes(fileExtension)) {
        return 'File harus berupa .docx';
    }
    
    if (file.size > 5 * 1024 * 1024) { // 5MB
        return 'Ukuran file maksimal 5MB';
    }
    
    return null; // Valid
}

const dz = new Dropzone("#fileUploadDropzone", {
    url: myForm.action,
    autoProcessQueue: false,
    maxFiles: 1,
    maxFilesize: 5, // 5MB
    acceptedFiles: ".docx",
    paramName: "file_soal",
    headers: { 
        'X-CSRF-TOKEN': "{{ csrf_token() }}" 
    },
    dictDefaultMessage: 'Klik atau drag file .docx ke sini<br><small>File maksimal 5MB</small>',
    dictInvalidFileType: 'File harus berupa .docx',
    dictFileTooBig: 'File terlalu besar (maksimal 5MB)',
    dictMaxFilesExceeded: 'Hanya bisa upload 1 file',
    
    init: function () {
        const dzInstance = this;
        
        // Event ketika tombol submit diklik
        btnSubmit.addEventListener("click", function () {
            // Validasi form inputs
            const judul = document.querySelector('input[name="judul"]').value;
            const tipe = document.querySelector('select[name="tipe"]').value;
            
            if (!judul.trim()) {
                Swal.fire("Oops", "Judul harus diisi!", "warning");
                return;
            }
            
            if (!tipe) {
                Swal.fire("Oops", "Tipe harus dipilih!", "warning");
                return;
            }
            
            if (dzInstance.getAcceptedFiles().length === 0) {
                Swal.fire("Oops", "Silakan upload file .docx terlebih dahulu!", "warning");
                return;
            }
            
            // Validasi file kunci jika ada
            const fileKunci = document.querySelector('input[name="file_kunci"]').files[0];
            if (fileKunci) {
                const validation = validateDocxFile(fileKunci);
                if (validation) {
                    Swal.fire("Oops", validation + " untuk file kunci jawaban!", "warning");
                    return;
                }
            }
            
            // Tampilkan loading
            Swal.fire({
                title: 'Uploading...',
                text: 'Sedang mengupload file, mohon tunggu...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            dzInstance.processQueue();
        });
        
        // Event ketika file ditambahkan
        dzInstance.on("addedfile", function(file) {
            const validation = validateDocxFile(file);
            if (validation) {
                Swal.fire("File Tidak Valid", validation, "error");
                dzInstance.removeFile(file);
                return;
            }
        });
        
        // Event ketika mengirim data
        dzInstance.on("sending", function(file, xhr, formData) {
            // Tambahkan semua input form ke formData
            const inputs = myForm.querySelectorAll("input, textarea, select");
            inputs.forEach(input => {
                if (input.name && input.type !== "file" && input.name !== "file_soal") {
                    formData.append(input.name, input.value);
                }
            });
            
            // Tambahkan file kunci jika ada
            const fileKunci = document.querySelector('input[name="file_kunci"]').files[0];
            if (fileKunci) {
                formData.append("file_kunci", fileKunci);
            }
        });
        
        // Event ketika berhasil
        dzInstance.on("success", function (file, response) {
            Swal.close();
            if (response.success) {
                Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil', 
                    text: 'Tugas berhasil diupload!', 
                    showConfirmButton: false, 
                    timer: 2000 
                });
                setTimeout(() => { 
                    window.location.reload(); 
                }, 2000);
            } else {
                Swal.fire("Gagal", response.message || "Upload gagal", "error");
            }
        });
        
        // Event ketika error
        dzInstance.on("error", function (file, response) {
            Swal.close();
            console.error('Upload error:', response);
            
            let message = "Upload gagal";
            
            if (typeof response === 'object') {
                if (response.errors) {
                    // Laravel validation errors
                    const errors = Object.values(response.errors).flat();
                    message = errors.join(', ');
                } else if (response.message) {
                    message = response.message;
                }
            } else if (typeof response === 'string') {
                message = response;
            }
            
            Swal.fire("Gagal", message, "error");
            dzInstance.removeFile(file);
        });
        
        // Event ketika upload selesai (berhasil atau gagal)
        dzInstance.on("complete", function() {
            // Reset form jika diperlukan
        });
    }
});

// Validasi file kunci jawaban saat dipilih
document.querySelector('input[name="file_kunci"]').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const validation = validateDocxFile(file);
        if (validation) {
            Swal.fire("File Tidak Valid", validation + " untuk file kunci jawaban!", "error");
            this.value = '';
        }
    }
});
</script>
@endsection