@extends('layouts.dosen')

@section('content')
<div class="container">
    <h3>Materi untuk Kelas: {{ $kelas->nama_kelas }}</h3>

    {{-- Alert Success/Error --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form Upload Materi --}}
    <div class="card mb-4">
        <div class="card-header">
            Upload Materi
        </div>
        <div class="card-body">

            <form method="POST" action="{{ route('dosen.materi.store', $kelas->id) }}" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <div class="mb-3">
                    <label>Judul Materi</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Tipe Materi</label>
                    <select name="tipe" id="tipe" class="form-control" required>
                        <option value="pdf">Upload PDF</option>
                        <option value="link">Link YouTube</option>
                    </select>
                </div>

                <div id="pdfField" class="mb-3">
                    <label>Upload File PDF (Drag and Drop atau Klik Area Bawah Ini)</label>
                    <div class="dropzone" id="pdfDropzone"></div>
                </div>

                <div id="linkField" class="mb-3" style="display: none;">
                    <label>Link YouTube</label>
                    <input type="url" name="link" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary mt-3">Upload Materi</button>
            </form>

        </div>
    </div>

    {{-- Button Back --}}
    <a href="{{ route('dosen.kelas.index') }}" class="btn btn-secondary">Kembali ke Daftar Kelas</a>

    {{-- List Materi --}}
    <form method="POST" action="{{ route('dosen.materi.bulkDelete') }}" id="bulkDeleteForm">
        @csrf
        @method('DELETE')

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <input type="checkbox" id="selectAll" class="form-check-input me-2">
                    <label for="selectAll" class="mb-0">Checklist Semua</label>
                </div>
                <div>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus Terpilih</button>
                </div>
            </div>

            <div class="card-body">
                @if($materis->count() > 0)
                    <ul class="list-group">
                        @foreach($materis as $materi)
                            <li class="list-group-item">
                                <input type="checkbox" name="materi_ids[]" value="{{ $materi->id }}" class="form-check-input me-2">
                                {{ $materi->judul }} ({{ ucfirst($materi->tipe) }})

                                @if($materi->tipe == 'pdf')
                                    <a href="{{ asset('storage/' . $materi->file) }}" target="_blank">[Lihat PDF]</a>
                                @else
                                    <a href="{{ $materi->link }}" target="_blank">[Tonton Video]</a><br>
                                    {{-- Tampilkan thumbnail YouTube --}}
                                    <img src="https://img.youtube.com/vi/{{ substr($materi->link, strpos($materi->link, 'v=') + 2, 11) }}/0.jpg" alt="Thumbnail" width="150" class="mt-2">
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $materis->links() }}
                    </div>
                @else
                    <p class="text-muted">Belum ada materi yang diupload.</p>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Pastikan ini adalah urutan yang benar untuk memuat Dropzone.js -->
<!-- Dropzone.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">

<!-- Dropzone.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<script>
// Menonaktifkan auto-discovery Dropzone
Dropzone.autoDiscover = false;

// Inisialisasi Dropzone
const pdfDropzone = new Dropzone("#pdfDropzone", {
    url: "{{ route('dosen.materi.store', $kelas->id) }}",  // URL untuk mengirim file
    paramName: "file",  // Nama parameter untuk file
    maxFiles: 1,  // Hanya izinkan 1 file
    acceptedFiles: "application/pdf",  // Hanya terima file PDF
    addRemoveLinks: true,  // Menambahkan tombol untuk menghapus file
    autoProcessQueue: false,  // Nonaktifkan auto-upload
    headers: {
        'X-CSRF-TOKEN': "{{ csrf_token() }}"  // CSRF Token untuk keamanan
    },
    init: function() {
        // Menambahkan data tambahan (judul, tipe, dan link) saat file dikirim
        this.on("sending", function(file, xhr, formData) {
            formData.append("judul", document.querySelector('input[name="judul"]').value);
            formData.append("tipe", document.querySelector('select[name="tipe"]').value);
            formData.append("link", document.querySelector('input[name="link"]').value);
        });

        // Ketika file berhasil dimasukkan
        this.on("addedfile", function(file) {
            console.log("File ditambahkan: ", file);
        });

        // Ketika file berhasil diupload
        this.on("success", function(file, response) {
            alert('Materi berhasil diupload!');
            location.reload();  // Refresh halaman setelah upload berhasil
        });

        // Ketika ada error
        this.on("error", function(file, response) {
            alert('Gagal upload! ' + response);
        });
    }
});

// Fungsi untuk mulai upload ketika tombol "Upload Materi" diklik
document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
    e.preventDefault();  // Cegah form untuk mengirimkan data secara otomatis

    // Periksa apakah judul materi sudah diisi
    const judulMateri = document.querySelector('input[name="judul"]').value;
    if (!judulMateri) {
        alert('Judul materi harus diisi sebelum mengupload file!');
        return;  // Cegah upload file jika judul belum diisi
    }

    const tipeMateri = document.querySelector('select[name="tipe"]').value;
    if (tipeMateri === 'pdf') {
        // Jika tipe materi adalah PDF, kirim file menggunakan Dropzone
        pdfDropzone.processQueue();  // Proses upload file yang sudah dimasukkan
    } else if (tipeMateri === 'link') {
        // Jika tipe materi adalah Link YouTube, kirim form secara manual
        document.getElementById('uploadForm').submit();
    }
});

// Script untuk toggle antara PDF dan Link YouTube
document.getElementById('tipe').addEventListener('change', function() {
    const pdfField = document.getElementById('pdfField');
    const linkField = document.getElementById('linkField');
    if (this.value === 'pdf') {
        pdfField.style.display = 'block';
        linkField.style.display = 'none';
    } else {
        pdfField.style.display = 'none';
        linkField.style.display = 'block';
    }
});
</script>
@endsection
