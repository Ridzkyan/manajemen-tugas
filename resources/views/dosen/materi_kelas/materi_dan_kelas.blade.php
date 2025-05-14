@extends('layouts.dosen')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container py-4">
    {{-- Judul --}}
    <div class="text-center mb-4">
        <h3 class="fw-bold">
            <i class="fas fa-chalkboard-teacher text-warning me-2"></i>
            Pengelolaan Materi dan Kelas
        </h3>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif

    {{-- DAFTAR KELAS KELOMPOK --}}
    <div class="mb-4">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-layer-group text-primary me-2"></i>
            Daftar Kelas yang Diajarkan
        </h6>

        {{-- FILTER & SEARCH --}}
        <div class="row align-items-center mb-3">
            <div class="col-md-3">
                <select id="filter_kategori" class="form-select form-select-sm shadow-sm">
                    <option value="">Semua Kelas</option>
                    @foreach(range('A', 'Z') as $char)
                        <option value="{{ $char }}">{{ $char }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="search_matkul" class="form-control form-control-sm shadow-sm" placeholder="Ketik nama mata kuliah...">
            </div>
        </div>

        @forelse($kelasGrouped as $namaKelas => $kelasList)
            <div class="mb-4 kelas-kategori" data-kategori="{{ $namaKelas }}">
                <h5 class="fw-bold text-teal mb-3">{{ $namaKelas }}</h5>
                <div class="row">
                    @foreach($kelasList as $kls)
                        <div class="col-md-4 mb-3 kelas-item"
                             data-kategori="{{ $namaKelas }}"
                             data-matkul="{{ strtolower($kls->nama_matakuliah) }}">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-3">
                                        <div class="text-muted">
                                            <i class="fas fa-book me-1"></i> {{ $kls->nama_matakuliah }}
                                        </div>
                                        <span class="badge bg-secondary mt-2">Materi: {{ $kls->materi->count() }}</span>
                                    </div>
                                    <a href="{{ route('dosen.materi_kelas.detail', ['id' => $kls->id, 'slug' => Str::slug($kls->nama_matakuliah)]) }}"
                                       class="btn btn-sm text-white mt-auto"
                                       style="background-color: #f5a04e;">
                                        <i class="fas fa-eye me-1"></i> Lihat Materi
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i> Belum ada kelas yang dibuat.
            </div>
        @endforelse
    </div>

    {{-- FORM UNGGAH MATERI --}}
    <div class="card shadow-sm border-0 mt-5">
        <div class="card-body">
            <h6 class="fw-bold mb-4 text-primary">
                <i class="fas fa-upload me-2 text-info"></i> Form Unggah Materi
            </h6>
            <form action="{{ route('dosen.materi_kelas.upload', $kelasPertama->id ?? 1) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-heading me-1 text-secondary"></i> Judul Materi
                    </label>
                    <input type="text" name="judul" class="form-control shadow-sm" required placeholder="Masukkan judul materi">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-layer-group me-1 text-secondary"></i> Pilih Kelas
                    </label>
                    <select name="kelas_id" class="form-select shadow-sm" required>
                        <option value="" disabled selected>Pilih kelas</option>
                        @foreach($kelasGrouped as $kelasList)
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}">{{ $kls->nama_kelas }} - {{ $kls->nama_matakuliah }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-file-alt me-1 text-secondary"></i> Tipe Materi
                    </label>
                    <select name="tipe" id="tipe" class="form-select shadow-sm" required>
                        <option value="pdf">PDF</option>
                        <option value="link">Link YouTube</option>
                    </select>
                </div>

                <div class="mb-3" id="pdfField">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-file-upload me-1 text-secondary"></i> Unggah File
                    </label>
                    <input type="file" name="file" accept="application/pdf" class="form-control shadow-sm">
                </div>

                <div class="mb-3 d-none" id="linkField">
                    <label class="form-label fw-semibold">
                        <i class="fab fa-youtube me-1 text-danger"></i> Link YouTube
                    </label>
                    <input type="url" name="link" class="form-control shadow-sm" placeholder="https://youtube.com/...">
                </div>

                <button type="submit" class="btn btn-warning text-white fw-semibold shadow-sm px-4">
                    <i class="fas fa-paper-plane me-1"></i> Unggah Materi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('tipe')?.addEventListener('change', function () {
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

    // Filter dan Search
    document.addEventListener('DOMContentLoaded', function () {
        const filterKategori = document.getElementById('filter_kategori');
        const searchMatkul = document.getElementById('search_matkul');

        function filterKelas() {
            const selectedKategori = filterKategori.value.toLowerCase();
            const keyword = searchMatkul.value.toLowerCase();

            document.querySelectorAll('.kelas-kategori').forEach(function (group) {
                const kategori = group.dataset.kategori.toLowerCase();
                let visibleItems = 0;

                group.querySelectorAll('.kelas-item').forEach(function (item) {
                    const matkul = item.dataset.matkul;
                    const cocokKategori = !selectedKategori || kategori === selectedKategori;
                    const cocokMatkul = !keyword || matkul.includes(keyword);

                    const tampil = cocokKategori && cocokMatkul;
                    item.style.display = tampil ? 'block' : 'none';

                    if (tampil) visibleItems++;
                });

                group.style.display = visibleItems > 0 ? 'block' : 'none';
            });
        }

        filterKategori.addEventListener('change', filterKelas);
        searchMatkul.addEventListener('input', filterKelas);
    });
</script>
@endpush