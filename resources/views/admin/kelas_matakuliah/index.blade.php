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

        <input type="text" class="form-control w-auto" id="searchBox" placeholder="Cari Mata Kuliah..." style="display: none;">
    </div>

    @php
        $groupedKelas = $daftarKelas->sortBy('nama_kelas')->groupBy('nama_kelas');
    @endphp

    @forelse($groupedKelas as $namaKelas => $kelasList)
        <div class="card kelas-wrapper kelas-{{ $namaKelas }}">
            <div class="card-header bg-white fw-semibold fs-6 d-flex justify-content-between align-items-center kelas-header toggle-kelas-header" data-header-target="{{ $namaKelas }}" style="cursor: pointer;">
                <span><i class="fas fa-door-open text-warning me-2"></i> Kelas {{ $namaKelas }}</span>
                <span class="toggle-kelas" data-target="{{ $namaKelas }}" style="cursor: pointer;">
                    <i class="fas fa-chevron-down rotate-icon"></i>
                </span>
            </div>
            <div class="card-body kelas-body kelas-body-{{ $namaKelas }}">
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

{{-- Style --}}
<style>
    .rotate-icon {
        transition: transform 0.3s ease;
    }

    .rotate-up {
        transform: rotate(180deg);
    }

    .kelas-body {
        overflow: hidden;
        transition: all 0.4s ease;
        max-height: 0;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        opacity: 0;
        visibility: hidden;
    }

    .kelas-body.active {
        max-height: 1000px;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        opacity: 1;
        visibility: visible;
    }

    .card.kelas-wrapper {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .kelas-header,
    .kelas-body .table-responsive {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }

    .table {
        margin-bottom: 0;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .table thead th,
    .table tbody td {
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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

            if (!body.classList.contains('active')) {
                body.classList.add('active');
                icon?.classList.add('rotate-up');
            }

            const rows = body.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const content = row.textContent.toLowerCase();
                row.style.display = content.includes(keyword) ? '' : 'none';
            });
        });

        // Klik ikon panah
        document.querySelectorAll('.toggle-kelas').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const target = this.dataset.target;
                const body = document.querySelector('.kelas-body-' + target);
                const icon = this.querySelector('i');

                body.classList.toggle('active');
                icon.classList.toggle('rotate-up');
            });
        });

        // Klik seluruh header
        document.querySelectorAll('.toggle-kelas-header').forEach(header => {
            header.addEventListener('click', function (e) {
                if (e.target.closest('.toggle-kelas')) return;

                const target = this.dataset.headerTarget;
                const body = document.querySelector('.kelas-body-' + target);
                const icon = this.querySelector('.toggle-kelas i');

                body.classList.toggle('active');
                icon?.classList.toggle('rotate-up');
            });
        });
    });
</script>
@endpush