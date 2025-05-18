@extends('layouts.dosen')

@section('content')
<div class="container">
    <div class="judul-whatsapp">
        <i class="bi bi-chat-dots-fill me-2 icon-teal"></i>
        <span class="teks-orange">Grup WhatsApp Kelas</span>
    </div>

    {{-- Filter Kategori Saja --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <select id="filterKelas" class="form-select shadow-sm">
                <option value="all">📂 Semua Kelas</option>
                @foreach($kelasGrouped->keys()->sort() as $kategori)
                    <option value="{{ strtolower($kategori) }}">Kelas {{ $kategori }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Daftar Kelas --}}
        <div id="kelasList">
            @forelse($kelasGrouped->sortKeys() as $kategori => $daftar)
                <div class="kategori-group mb-4" data-kategori="{{ strtolower($kategori) }}">
                    <h5 class="fw-semibold mb-3 text-muted">🗂️ Kelas {{ $kategori }}</h5>
                    <div class="list-group shadow-sm">
                        @foreach($daftar as $kls)
                            <div class="list-group-item d-flex justify-content-between align-items-center item-kelas">
                                <div class="d-flex align-items-start flex-column">
                                    <span class="fw-bold">
                                        <i class="bi bi-mortarboard-fill me-2 text-teal"></i> {{ $kls->nama_kelas }}
                                    </span>
                                    <small class="text-secondary mt-1">
                                        <i class="bi bi-book me-1"></i> {{ $kls->nama_matakuliah }}
                                    </small>
                                </div>
                                <div>
                                    @if($kls->whatsapp_link)
                                        <a href="{{ $kls->whatsapp_link }}" target="_blank" class="btn btn-success btn-sm">
                                            <i class="bi bi-whatsapp me-1"></i> Join WhatsApp
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Tidak Ada Link</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-muted">Tidak ada data kelas yang tersedia.</p>
            @endforelse
        </div>
</div>
@endsection

<style>
    .judul-whatsapp {
        text-align: center;
        margin-bottom: 5.5rem;
    }

    .judul-whatsapp .icon-teal {
        color: #f5a04e;
        font-size: 1.8rem;
        vertical-align: middle;
    }

    .judul-whatsapp .teks-orange {
        color: #000000;
        font-weight: 700;
        font-size: 2rem;
        vertical-align: middle;
    }

    .text-teal {
        color: #008080;
    }

    .list-group-item {
        border-left: 4px solid #00808020;
        margin-bottom: 8px;
    }

    .btn-success {
        background-color: #25D366;
        border: none;
    }

    .btn-success:hover {
        background-color: #20b257;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterSelect = document.getElementById('filterKelas');

        function filterKelas() {
            const selectedKategori = filterSelect.value;

            document.querySelectorAll('.kategori-group').forEach(group => {
                const kategori = group.getAttribute('data-kategori');
                const isVisible = (selectedKategori === 'all' || kategori === selectedKategori);
                group.style.display = isVisible ? '' : 'none';
            });
        }

        filterSelect.addEventListener('change', filterKelas);
        filterKelas(); // inisialisasi saat pertama load
    });
</script>
@endpush