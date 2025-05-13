@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Pengelolaan Materi dan Kelas</h4>
        
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

   {{-- DAFTAR KELAS --}}
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Daftar Kelas yang Diajarkan</h6>
        <ul class="list-unstyled mb-0">
            @forelse($kelas as $kls)
                <li class="mb-1">
                    <a href="{{ route('dosen.materi_kelas.detail', $kls->id) }}" class="text-primary text-decoration-none">
                        {{ $kls->nama_matakuliah }}
                    </a>
                </li>
            @empty
                <li class="text-muted">Belum ada kelas.</li>
            @endforelse
        </ul>
    </div>
</div>


    {{-- FORM UNGGAH MATERI --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Form Unggah Materi</h6>
            <form action="{{ route('dosen.materi_kelas.upload', $kelasPertama->id ?? 1) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul Materi</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Kelas</label>
                    <select name="kelas_id" class="form-select" required>
                        <option value="" disabled selected>Pilih kelas</option>
                        @foreach($kelas as $kls)
                            <option value="{{ $kls->id }}">{{ $kls->nama_matakuliah }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Materi</label>
                    <select name="tipe" id="tipe" class="form-select" required>
                        <option value="pdf">PDF</option>
                        <option value="link">Link YouTube</option>
                    </select>
                </div>

                <div class="mb-3" id="pdfField">
                    <label class="form-label">Unggah File</label>
                    <input type="file" name="file" accept="application/pdf" class="form-control">
                </div>

                <div class="mb-3 d-none" id="linkField">
                    <label class="form-label">Link YouTube</label>
                    <input type="url" name="link" class="form-control">
                </div>

                <button type="submit" class="btn btn-warning text-white">
                    <i class="bi bi-upload"></i> Unggah materi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle field
    document.getElementById('tipe').addEventListener('change', function() {
        const pdfField = document.getElementById('pdfField');
        const linkField = document.getElementById('linkField');
        if (this.value === 'link') {
            pdfField.classList.add('d-none');
            linkField.classList.remove('d-none');
        } else {
            pdfField.classList.remove('d-none');
            linkField.classList.add('d-none');
        }
    });
</script>
@endsection
