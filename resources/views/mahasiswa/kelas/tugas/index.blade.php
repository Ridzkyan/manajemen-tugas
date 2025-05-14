@extends('layouts.mahasiswa')
@section('title', 'Tugas & Ujian')

@section('content')
<h4 class="fw-bold mb-4">Tugas & Ujian</h4>

@forelse($tugas as $tgs)
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-{{ $tgs->tipe == 'ujian' ? 'warning' : 'primary' }} text-white">
            <div>{{ ucfirst($tgs->tipe) }}: {{ $tgs->judul }}</div>
            <div>
                @if(in_array($tgs->id, $pengumpulanTugas))
                    <span class="badge bg-success">✅ Terkumpul</span>
                @else
                    <span class="badge bg-danger">❌ Belum</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <p><strong>Kelas:</strong> {{ $kelas->nama_matakuliah }}</p>
            <p><strong>Deskripsi:</strong> {{ $tgs->deskripsi }}</p>
            <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($tgs->deadline)->format('d M Y') }}</p>

            @if(!in_array($tgs->id, $pengumpulanTugas))
                <button class="btn btn-sm btn-warning kerjakan-btn" 
                    data-tugas="{{ $tgs->id }}" 
                    data-kelas="{{ $kelas->id }}">
                    Kerjakan
                </button>
            @else
                <a href="{{ route('mahasiswa.tugas.preview', ['kelas' => $kelas->id, 'tugas' => $tgs->id]) }}" class="btn btn-sm btn-info">Lihat/Download</a>
                <form action="{{ route('mahasiswa.tugas.delete', ['kelas' => $kelas->id, 'tugas' => $tgs->id]) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            @endif
        </div>
    </div>
@empty
    <p class="text-muted">Belum ada tugas di kelas ini.</p>
@endforelse

{{-- Modal drag n drop --}}
<div id="modal-dropzone" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" id="dropzone-form">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Upload Tugas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="dropzone" id="dropzone-area"></div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Dropzone --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-...SHA..." crossorigin="anonymous"></script>

<script>
Dropzone.autoDiscover = false;
let dropzoneInstance;

document.addEventListener("DOMContentLoaded", function () {
    const modal = new bootstrap.Modal(document.getElementById('modal-dropzone'));
    const dropzoneArea = document.getElementById('dropzone-area');
    const form = document.getElementById('dropzone-form');

    document.querySelectorAll('.kerjakan-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const kelasId = this.dataset.kelas;
            const tugasId = this.dataset.tugas;
            form.action = `/mahasiswa/kelas/${kelasId}/tugas/${tugasId}/upload`;

            dropzoneArea.innerHTML = '';
            if (dropzoneInstance) dropzoneInstance.destroy();

            dropzoneInstance = new Dropzone("#dropzone-area", {
                url: form.action,
                paramName: "file_tugas",
                maxFiles: 1,
                acceptedFiles: ".pdf,.doc,.docx,.zip",
                addRemoveLinks: true,
                autoProcessQueue: false,
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                init: function () {
                    let dz = this;
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        if (dz.getAcceptedFiles().length === 0) {
                            alert("Silakan unggah file terlebih dahulu.");
                        } else {
                            dz.processQueue();
                        }
                    });
                    dz.on("success", function () {
                        alert("✅ Tugas berhasil diunggah!");
                        modal.hide();
                        location.reload();
                    });
                    dz.on("error", function () {
                        alert("❌ Gagal mengunggah file.");
                    });
                }
            });

            modal.show();
        });
    });
});
</script>
@endsection
