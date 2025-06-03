@extends('layouts.dosen')

@section('content')

<link href="{{ asset('css/backsite/dosen/kelola_kelas.css') }}" rel="stylesheet">

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