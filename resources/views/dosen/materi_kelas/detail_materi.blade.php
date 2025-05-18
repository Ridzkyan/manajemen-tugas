@extends('layouts.dosen')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
    .container {
        font-family: 'Poppins', sans-serif;
    }
    .card-materi {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        border: none;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease forwards;
    }
    .card-materi h5 {
        color: #000;
    }
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container py-4">
    {{-- Header --}}
    <div class="mb-4">
        <div class="d-flex align-items-center mb-1">
            <i class="fas fa-chalkboard-teacher text-warning me-2 fa-lg"></i>
            <h4 class="fw-bold text-dark mb-0">{{ $kelas->nama_kelas }} - {{ $kelas->nama_matakuliah }}</h4>
        </div>
        <div class="d-flex align-items-center">
            <i class="fas fa-folder text-primary me-2"></i>
            <a href="{{ route('dosen.materi_kelas.index') }}" class="text-decoration-none" style="color: #008080; font-weight: 600;">Daftar Materi</a>
        </div>
        <hr class="mt-2" style="border-top: 2px solid #fde7cd; max-width: 250px;">
    </div>

    {{-- Materi List --}}
    @forelse($materis as $index => $materi)
        <div class="card card-materi mb-4" style="animation-delay: {{ 0.1 * $index }}s;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        @if($materi->tipe === 'link')
                            <i class="fas fa-play-circle text-danger me-2"></i>
                        @elseif($materi->tipe === 'pdf')
                            <i class="fas fa-file-pdf text-danger me-2"></i>
                        @endif
                        {{ $materi->judul }}
                    </h5>
                    <div class="btn-group">
                        @if($materi->tipe === 'link')
                            <a href="{{ $materi->link }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka Link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $materi->file) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka PDF">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @endif

                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleEditForm({{ $materi->id }})">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $materi->id }})">
                            <i class="fas fa-trash-alt"></i>
                        </button>

                        <form id="delete-form-{{ $materi->id }}" action="{{ route('dosen.materi_kelas.destroy', $materi->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>

                {{-- Preview --}}
                @if($materi->tipe === 'link')
                    <div class="ratio ratio-16x9 mb-3">
                        <iframe src="{{ $materi->link }}" frameborder="0" allowfullscreen></iframe>
                    </div>
                @elseif($materi->tipe === 'pdf')
                    <div class="mb-3">
                        <iframe src="{{ asset('storage/' . $materi->file) }}" width="100%" height="500px" style="border: 1px solid #ccc; border-radius: 8px;"></iframe>
                    </div>
                @endif

                <div class="text-muted small mb-2">
                    <i class="far fa-calendar-alt me-1"></i>
                    Diunggah pada: {{ $materi->created_at->format('d M Y') }}
                </div>

                {{-- Inline Edit Form --}}
                <form id="edit-form-{{ $materi->id }}" class="border-top pt-3 mt-3 d-none" method="POST" action="{{ route('dosen.materi_kelas.update', $materi->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-2">
                        <label class="form-label">Judul Materi</label>
                        <input type="text" name="judul" class="form-control form-control-sm" value="{{ $materi->judul }}" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Tipe</label>
                        <select name="tipe" class="form-select form-select-sm" onchange="toggleEditTipe(this, {{ $materi->id }})">
                            <option value="pdf" {{ $materi->tipe === 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="link" {{ $materi->tipe === 'link' ? 'selected' : '' }}>Link</option>
                        </select>
                    </div>

                    <div class="mb-2 edit-link-{{ $materi->id }} {{ $materi->tipe !== 'link' ? 'd-none' : '' }}">
                        <label class="form-label">Link YouTube</label>
                        <input type="url" name="link" class="form-control form-control-sm" value="{{ $materi->link }}">
                    </div>

                    <div class="mb-2 edit-pdf-{{ $materi->id }} {{ $materi->tipe !== 'pdf' ? 'd-none' : '' }}">
                        <label class="form-label">Ganti File PDF (opsional)</label>
                        <input type="file" name="file" accept="application/pdf" class="form-control form-control-sm">
                    </div>

                    <button type="submit" class="btn btn-sm btn-success mt-2">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-1"></i> Belum ada materi yang tersedia untuk kelas ini.
        </div>
    @endforelse

    {{-- Tombol kembali --}}
    <a href="{{ route('dosen.materi_kelas.index') }}" class="btn btn-secondary mt-4">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleEditForm(id) {
        document.getElementById(`edit-form-${id}`).classList.toggle('d-none');
    }

    function toggleEditTipe(select, id) {
        const isLink = select.value === 'link';
        document.querySelector(`.edit-link-${id}`).classList.toggle('d-none', !isLink);
        document.querySelector(`.edit-pdf-${id}`).classList.toggle('d-none', isLink);
    }

    function confirmDelete(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Materi yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endpush