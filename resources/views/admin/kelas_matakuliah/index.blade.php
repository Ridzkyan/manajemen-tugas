@extends('layouts.admin')

@section('title', 'Kelas / Mata Kuliah')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold text-dark">
        <i class="fas fa-folder-open text-warning me-2"></i>Daftar Kelas / Mata Kuliah
    </h4>

    @if(session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    <!-- Filter & Search -->
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <select class="form-select w-auto" id="filterKelas">
            <option value="">Semua Kelas</option>
            @foreach(range('A', 'Z') as $abjad)
                <option value="{{ $abjad }}">Kelas {{ $abjad }}</option>
            @endforeach
        </select>

        <input type="text" class="form-control w-auto" id="searchBox" placeholder="Cari kelas..." style="display: none;">
    </div>

    @php
        $groupedKelas = $daftarKelas->sortBy('nama_kelas')->groupBy('nama_kelas');
    @endphp

    @forelse($groupedKelas as $namaKelas => $kelasList)
        <div class="card border-0 shadow-sm rounded-4 mb-4 kelas-wrapper kelas-{{ $namaKelas }}">
            <div class="card-header bg-white fw-semibold fs-6 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-door-open text-warning me-2"></i> Kelas {{ $namaKelas }}</span>
                <button class="btn btn-sm btn-outline-secondary toggle-kelas" data-target="{{ $namaKelas }}">
                    <i class="fas fa-chevron-down rotate-icon"></i>
                </button>
            </div>
            <div class="card-body px-0 pt-0 pb-3 kelas-body kelas-body-{{ $namaKelas }}">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Kode Unik</th>
                                <th>Mahasiswa</th>
                                <th>Materi</th>
                                <th>Tugas</th>
                                <th>Grup WA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelasList as $index => $kelas)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $kelas->nama_matakuliah }}</td>
                                    <td>{{ $kelas->dosen->name ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ $kelas->kode_unik }}</span></td>
                                    <td>{{ $kelas->mahasiswa->count() }}</td>
                                    <td>{{ $kelas->materis->count() }}</td>
                                    <td>{{ $kelas->tugas->count() }}</td>
                                    <td>
                                        @if($kelas->whatsapp_link)
                                            <a href="{{ $kelas->whatsapp_link }}" target="_blank" class="btn btn-sm btn-success">
                                                <i class="fab fa-whatsapp me-1"></i>Buka
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-4">Belum ada kelas yang terdaftar.</div>
    @endforelse
</div>

{{-- Tambahan style animasi --}}
<style>
    .rotate-icon {
        transition: transform 0.3s ease;
    }

    .rotate-up {
        transform: rotate(180deg);
    }

    .kelas-body {
        overflow: hidden;
        transition: max-height 0.4s ease;
        max-height: 0;
        padding: 0 !important;
    }

    .kelas-body.active {
        max-height: 800px; /* Sesuaikan dengan isi maksimum */
        padding: 1rem 0 !important;
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filter = document.getElementById('filterKelas');
        const searchBox = document.getElementById('searchBox');

        // Sembunyikan semua kelas di awal
        document.querySelectorAll('.kelas-body').forEach(body => {
            body.classList.remove('active');
        });

        document.querySelectorAll('.toggle-kelas').forEach(btn => {
            const icon = btn.querySelector('i');
            icon.classList.remove('rotate-up');
        });

        // Filter kelas
        filter.addEventListener('change', function () {
            const value = this.value;
            searchBox.style.display = value ? 'inline-block' : 'none';

            document.querySelectorAll('.kelas-wrapper').forEach(card => {
                card.style.display = !value || card.classList.contains('kelas-' + value) ? '' : 'none';
            });

            searchBox.value = '';
        });

        // Pencarian
        searchBox.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const selected = filter.value;
            if (!selected) return;

            const body = document.querySelector(`.kelas-body-${selected}`);
            const toggleBtn = document.querySelector(`.toggle-kelas[data-target="${selected}"]`);
            const icon = toggleBtn?.querySelector('i');

            // Buka body jika belum terbuka
            if (!body.classList.contains('active')) {
                body.classList.add('active');
                icon?.classList.add('rotate-up');
            }

            // Filter isi tabel
            const rows = body.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const content = row.textContent.toLowerCase();
                row.style.display = content.includes(keyword) ? '' : 'none';
            });
        });

        // Toggle tampil/sembunyi kelas
        document.querySelectorAll('.toggle-kelas').forEach(btn => {
            btn.addEventListener('click', function () {
                const target = this.dataset.target;
                const body = document.querySelector('.kelas-body-' + target);
                const icon = this.querySelector('i');

                body.classList.toggle('active');
                icon.classList.toggle('rotate-up');
            });
        });
    });
</script>
@endpush