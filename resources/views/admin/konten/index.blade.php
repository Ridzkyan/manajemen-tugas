@extends('layouts.admin')

@section('title', 'Konten Terbaru')

@section('content')
<style>
    .filter-bar {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 1rem;
    }

    .filter-bar input,
    .filter-bar select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .konten-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        margin-bottom: 16px;
    }

    .konten-header {
        padding: 12px 20px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
    }

    .konten-header:hover {
        background-color: #f8f8f8;
    }

    .konten-body {
        display: none;
        padding: 10px 20px 20px;
    }

    .konten-section {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
    }

    .konten-section.tugas {
        background-color: #fef6f2;
    }

    .konten-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
        table-layout: fixed;
    }

    .konten-table th,
    .konten-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        vertical-align: middle;
        text-align: left;
        white-space: nowrap;
    }

    .konten-table th {
        background-color: #f8f8f8;
        font-weight: bold;
    }

    .btn-download {
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 20px;
        color: white;
        background-color: #28a745;
        border: none;
        text-decoration: none;
    }

    .btn-download:hover {
        background-color: #218838;
    }

    .text-muted {
        color: #999;
    }

    .section-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #f5a04e;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        font-size: 20px;
    }
</style>

<div class="container py-4">
    <div class="section-title">
        <i class="fas fa-file-alt text-orange"></i>
        Konten Terbaru
    </div>

    <form method="GET" class="filter-bar mb-3" id="filterForm">
        <select name="kategori" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <option value="materi" {{ request('kategori') == 'materi' ? 'selected' : '' }}>Materi</option>
            <option value="tugas" {{ request('kategori') == 'tugas' ? 'selected' : '' }}>Tugas</option>
        </select>
        <input type="text" name="matkul" id="matkulInput" placeholder="Cari matakuliah..." value="{{ request('matkul') }}">
    </form>

    @foreach($matkulList as $matkul)
        <div class="konten-card">
            <div class="konten-header" onclick="toggleBody(this)">
                <span>{{ $matkul->nama_matakuliah }}</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="konten-body">
                @if(request('kategori') != 'tugas')
                    <div class="konten-section">
                        <h6 class="text-uppercase fw-bold text-orange">Materi</h6>
                        <table class="konten-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Judul</th>
                                    <th style="width: 30%;">Diunggah Oleh</th>
                                    <th style="width: 20%;">Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($materiTerbaru->where('kelas.nama_matakuliah', $matkul->nama_matakuliah) as $materi)
                                    <tr>
                                        <td><i class="fas fa-file-alt me-2 text-success"></i>{{ $materi->judul }}</td>
                                        <td>{{ $materi->kelas->dosen->name ?? '-' }}</td>
                                        <td>
                                            @if($materi->file)
                                                <a href="{{ asset('storage/' . $materi->file) }}" target="_blank" class="btn-download">PDF</a>
                                            @endif
                                            @if($materi->link)
                                                <a href="{{ $materi->link }}" target="_blank" class="btn-download" style="background-color:#dc3545;">YouTube</a>
                                            @endif
                                            @if(!$materi->file && !$materi->link)
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(request('kategori') != 'materi')
                    <div class="konten-section tugas">
                        <h6 class="text-uppercase fw-bold text-purple">Tugas</h6>
                        <table class="konten-table">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Judul</th>
                                    <th style="width: 30%;">Diunggah Oleh</th>
                                    <th style="width: 20%;">Download</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tugasTerbaru->where('kelas.nama_matakuliah', $matkul->nama_matakuliah) as $tugas)
                                    <tr>
                                        <td><i class="fas fa-tasks me-2 text-purple"></i>{{ $tugas->judul }}</td>
                                        <td>{{ $tugas->kelas->dosen->name ?? '-' }}</td>
                                        <td>
                                            @if($tugas->file_soal)
                                                <a href="{{ asset('storage/' . $tugas->file_soal) }}" target="_blank" class="btn-download">PDF</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
    function toggleBody(header) {
        const body = header.nextElementSibling;
        const icon = header.querySelector('i');
        const isVisible = body.style.display === 'block';
        body.style.display = isVisible ? 'none' : 'block';
        icon.classList.toggle('fa-chevron-down', isVisible);
        icon.classList.toggle('fa-chevron-up', !isVisible);
    }

    window.addEventListener('DOMContentLoaded', () => {
        const scrollToTarget = () => {
            const matkul = '{{ request('matkul') }}';
            if (!matkul) return;
            const cards = document.querySelectorAll('.konten-card');
            for (let card of cards) {
                const title = card.querySelector('.konten-header span')?.textContent.toLowerCase();
                if (title && title.includes(matkul.toLowerCase())) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    break;
                }
            }
        };
        const kategori = '{{ request('kategori') }}';
        const matkul = '{{ request('matkul') }}';

        document.querySelectorAll('.konten-card').forEach(card => {
            const title = card.querySelector('.konten-header span')?.textContent.toLowerCase();
            const body = card.querySelector('.konten-body');
            const icon = card.querySelector('.konten-header i');

            // Reset semua
            body.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            card.style.display = 'block';

            if (matkul && title && title.includes(matkul.toLowerCase())) {
                body.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else if (matkul && !title.includes(matkul.toLowerCase())) {
                card.style.display = 'none';
            } else if (!matkul && kategori) {
                body.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });

        const matkulInput = document.getElementById('matkulInput');
        const form = document.getElementById('filterForm');
        let delay;

        matkulInput.addEventListener('input', function () {
            clearTimeout(delay);
            delay = setTimeout(() => {
                scrollToTarget();
                form.submit();
            }, 500);
        });
    });
</script>
@endpush