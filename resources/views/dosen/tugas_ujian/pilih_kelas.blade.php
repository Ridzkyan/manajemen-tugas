@extends('layouts.dosen')

@section('content')

<link href="{{ asset('css/backsite/dosen/pilih_kelas.css') }}" rel="stylesheet">
<div class="container py-4">
    {{-- Judul Tengah --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold">
            <i class="bi bi-journal-text me-2" style="color: #f5a04e;"></i>
            Pengelolaan Tugas dan Ujian
        </h4>
    </div>

    {{-- Filter dan Pencarian --}}
    <div class="mb-4">
        <label class="form-label fw-semibold fs-6 text-dark d-block mb-2">
            <i class="bi bi-layers me-1" style="color: #007bff;"></i> Daftar Kelas yang Diajarkan
        </label>

        <div class="d-flex flex-wrap gap-2 align-items-end">
            {{-- Filter Kategori --}}
            <select id="filterKategori" class="form-select form-select-sm shadow-sm" style="width: 200px;">
                <option value="">Semua Kelas</option>
                @foreach($kelasGrouped->keys() as $kategori)
                    <option value="{{ $kategori }}">Kelas {{ $kategori }}</option>
                @endforeach
            </select>

            {{-- Pencarian --}}
            <input type="text" id="searchInput" class="form-control form-control-sm shadow-sm"
                   placeholder="Ketik nama mata kuliah..." style="width: 250px;">
        </div>
    </div>

    {{-- Daftar Kelas --}}
    @foreach($kelasGrouped as $kategori => $kelasList)
        <h5 class="mt-4 fw-bold">Kelas {{ $kategori }}</h5>
        <div class="row">
            @foreach($kelasList as $kelas)
                <div class="col-md-4 mb-4 kelas-item">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body">
                            <div>
                                <h5 class="card-title mb-1">
                                    <i class="bi bi-book-fill me-2" style="color: #f5a04e;"></i>
                                    {{ $kelas->nama_matakuliah }}
                                </h5>
                                <p class="text-muted mb-1">Kategori: <strong>{{ $kategori }}</strong></p>

                                {{-- 📆 Deadline Tugas --}}
                                @if($kelas->deadline_terdekat)
                                    <p class="mb-2 text-secondary" style="font-size: 0.9rem;">
                                        <i class="bi bi-calendar-event me-1" style="color: #f5a04e;"></i>
                                        Deadline Terdekat:
                                        <strong>{{ \Carbon\Carbon::parse($kelas->deadline_terdekat)->translatedFormat('d F Y') }}</strong>
                                    </p>
                                @endif
                            </div>

                            <div class="mt-3 d-flex flex-column gap-2">
                                {{-- Jumlah tugas --}}
                                <span class="badge text-white" style="background-color: #008080; width: fit-content;">
                                    Tugas: {{ $kelas->tugas->count() ?? 0 }}
                                </span>

                                {{-- Tombol --}}
                                <a href="{{ route('dosen.tugas_ujian.index', $kelas->id) }}"
                                   class="btn btn-orange fw-semibold d-flex justify-content-center align-items-center gap-1"
                                   style="border-radius: 8px;">
                                    <i class="bi bi-eye-fill"></i> Lihat Tugas & Ujian
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

{{-- Script Filter & Search --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterKategori = document.getElementById('filterKategori');

        function filterKelas() {
            const keyword = searchInput.value.toLowerCase();
            const selectedKategori = filterKategori.value;

            document.querySelectorAll('.kelas-item').forEach(item => {
                const group = item.closest('.row');
                const kategori = group.previousElementSibling?.innerText?.replace('Kelas ', '') || '';
                const text = item.innerText.toLowerCase();
                const matchSearch = text.includes(keyword);
                const matchKategori = selectedKategori === '' || kategori === selectedKategori;

                item.style.display = matchSearch && matchKategori ? 'block' : 'none';
            });
        }

        searchInput.addEventListener('input', filterKelas);
        filterKategori.addEventListener('change', filterKelas);
    });
</script>
@endsection