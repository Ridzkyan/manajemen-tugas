@extends('layouts.dosen')

@section('content')

<style>
    .judul-kelas {
        text-align: center;
        font-weight: bold;
        margin-bottom: 5.5rem;
        font-size: 2.5rem;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
    }

    .icon-judul {
        font-size: 2.6rem;
        color: #f5a04e;
    }

    .btn-tambah-kelas {
        background-color: #008080;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 0.95rem;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-tambah-kelas i {
        font-size: 1rem;
    }

    .btn-tambah-kelas:hover {
        background-color: #f5a04e;
        color: white;
    }

    .kelas-header {
        background-color: #ffffff;
        border-left: 5px solid #008080;
        border-radius: 0.35rem 0.35rem 0 0;
        cursor: pointer;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
    }

    .kelas-header:hover {
        background-color: #f1f1f1;
    }

    .icon-folder {
        font-size: 1.2rem;
        color: #008080;
    }

    .arrow-icon {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .card.kategori-card {
        border: none;
        border-radius: 0.35rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .card-body {
        background-color: #ffffff;
        border-top: 1px solid #eee;
    }

    .btn-taskflow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
        border-radius: 10px;
        background-color: #6c757d !important;
        color: white !important;
        border: none !important;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-taskflow i {
        pointer-events: none;
    }

    .btn-taskflow:hover {
        transform: scale(1.1);
        opacity: 0.9;
    }

    .btn-mahasiswa {
        background: linear-gradient(135deg, #00bfa6, #008080) !important;
    }

    .btn-edit {
        background: linear-gradient(135deg, #f9b54c, #f39c12) !important;
    }

    .btn-hapus {
        background: linear-gradient(135deg, #f27065, #e74c3c) !important;
    }

    .btn-copy {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d) !important;
    }

    .btn-taskflow:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.2);
    }
</style>

<div class="container py-4">
    <h4 class="judul-kelas">
        <i class="bi bi-collection-play-fill icon-judul"></i>
        Daftar Kelas Saya
    </h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-3">
            <select id="filterKelas" class="form-select shadow-sm">
                <option value="">📂 Semua Kategori</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ strtolower($kategori) }}">Kelas {{ $kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Cari nama mata kuliah...">
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('dosen.kelola_kelas.create') }}" class="btn-tambah-kelas">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
            </a>
        </div>
    </div>

    <div id="kelasContainer">
        @forelse($kelasGrouped as $kategori => $daftar)
        <div class="card kategori-card shadow-sm mb-3" data-kategori="{{ strtolower($kategori) }}">
            <div class="card-header kelas-header d-flex justify-content-between align-items-center toggle-header"
                data-target="body-{{ $kategori }}">
                <div class="d-flex align-items-center gap-2">
                    <span class="icon-folder">📂</span>
                    <span class="fw-semibold">Kelas {{ $kategori }}</span>
                </div>
                <span class="arrow-icon" id="icon-{{ $kategori }}">⏷</span>
            </div>
            <div class="collapse" id="body-{{ $kategori }}">
                <div class="card-body bg-light rounded-bottom">
                    @foreach($daftar as $kls)
                    <div class="kelas-row d-flex justify-content-between align-items-center flex-wrap py-2 px-3 border-bottom"
                        data-kategori="{{ strtolower($kategori) }}"
                        data-matkul="{{ strtolower($kls->nama_matakuliah) }}">
                        <div class="fw-semibold" style="width: 12%; min-width: 80px">{{ $kls->nama_kelas }}</div>
                        <div style="width: 28%; min-width: 180px">{{ $kls->nama_matakuliah }}</div>
                        <div style="width: 25%; min-width: 150px">
                            {{ $kls->kode_unik ?? '-' }}
                            @if($kls->kode_unik)
                                <button class="btn-taskflow btn-copy ms-2 copy-btn" data-code="{{ $kls->kode_unik }}" title="Salin Kode">
                                    <i class="bi bi-clipboard-check-fill"></i>
                                </button>
                            @endif
                        </div>
                        <div class="d-flex gap-2 justify-content-end flex-wrap" style="width: 35%">
                            <a href="{{ route('dosen.kelola_kelas.show', $kls->id) }}" class="btn-taskflow btn-mahasiswa" title="Lihat Mahasiswa">
                                <i class="bi bi-person-vcard-fill"></i>
                            </a>
                            <a href="{{ route('dosen.kelola_kelas.edit', $kls->id) }}" class="btn-taskflow btn-edit" title="Edit Kelas">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form action="{{ route('dosen.kelola_kelas.destroy', $kls->id) }}" method="POST" class="d-inline form-delete">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-taskflow btn-hapus" title="Hapus Kelas">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="text-muted">Belum ada kelas yang dibuat.</div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 & Bootstrap Icons -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<script>
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const code = this.dataset.code;
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Kode berhasil disalin!',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            });
        });
    });

    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Kelas akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('.toggle-header').forEach(header => {
        header.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const target = document.getElementById(targetId);
            const icon = document.getElementById('icon-' + targetId.replace('body-', ''));

            target.classList.toggle('show');
            icon.textContent = target.classList.contains('show') ? '⏶' : '⏷';
        });
    });

    document.getElementById('filterKelas').addEventListener('change', filterTable);
    document.getElementById('searchInput').addEventListener('keyup', filterTable);

    function filterTable() {
        const selectedKategori = document.getElementById('filterKelas').value.toLowerCase().trim();
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();

        document.querySelectorAll('.kategori-card').forEach(card => {
            const cardKategori = card.dataset.kategori?.toLowerCase().trim();
            const collapse = card.querySelector('.collapse');
            const icon = card.querySelector('.arrow-icon');
            let anyVisible = false;

            card.querySelectorAll('.kelas-row').forEach(row => {
                const rowMatkul = (row.dataset.matkul || '').toLowerCase().trim();
                const cocokKategori = !selectedKategori || cardKategori === selectedKategori;
                const cocokMatkul = !searchText || rowMatkul.includes(searchText);

                const tampilkan = cocokKategori && cocokMatkul;
                row.classList.toggle('d-none', !tampilkan);
                if (tampilkan) anyVisible = true;
            });

            if (anyVisible) {
                collapse.classList.add('show');
                icon.textContent = "⏶";
                card.style.display = '';
            } else {
                collapse.classList.remove('show');
                icon.textContent = "⏷";
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection