@extends('layouts.mahasiswa')

@section('title', 'Tugas & Ujian')

@section('content')

{{-- Import CSS halaman tugas mahasiswa --}}
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/tugas_kelas.css') }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('tugas_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('tugas_success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
@endif

@if(session('tugas_error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('tugas_error') }}',
            showConfirmButton: true
        });
    </script>
@endif

<div class="container py-3">
    <h3 class="mb-4 text-center">
        <i class="bi bi-clipboard-check me-2 icon-f5a04e"></i>
        Daftar Tugas & Ujian
    </h3>

    {{-- TAB FILTER --}}
    @php
        $currentTipe = $tipe ?? 'tugas'; 
    @endphp

    <div class="filter-tab mb-4">
        <a href="{{ route('mahasiswa.kelas.tugas.index', [$kelas->id, 'tipe' => 'tugas']) }}"
           class="{{ $currentTipe === 'tugas' ? 'active' : '' }}">
            Tugas
        </a>
        <a href="{{ route('mahasiswa.kelas.tugas.index', [$kelas->id, 'tipe' => 'ujian']) }}"
           class="{{ $currentTipe === 'ujian' ? 'active' : '' }}">
            Ujian
        </a>
    </div>

    @php
        $pengumpulanTugasIds = $pengumpulanTugas->pluck('tugas_id')->toArray();
    @endphp

    @if($daftarTugas->count())
        <div class="row">
            @foreach($daftarTugas as $tgs)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card-tugas h-100">
                        <div class="tugas-header">{{ $tgs->judul }}</div>                        <div class="tugas-info"><strong>Kelas:</strong> {{ $kelas->nama_matakuliah ?? '-' }}</div>
                        <div class="tugas-info"><strong>Deskripsi:</strong> {{ $tgs->deskripsi ?? '-' }}</div>
                        <div class="tugas-info">
                            <strong>Deadline:</strong>
                            {{ $tgs->deadline ? \Carbon\Carbon::parse($tgs->deadline)->format('d M Y - H:i') : '-' }}
                        </div>

                        @php
                            $pengumpulan = $pengumpulanTugas[$tgs->id] ?? null;
                        @endphp

                        @if($pengumpulan)
                            <span class="badge-status badge-terkumpul">Terkumpul</span>

                            <div class="tugas-info mt-2">
                                <strong>Nilai:</strong> {{ $pengumpulan->nilai ?? 'Belum dinilai' }}
                            </div>
                            <div class="tugas-info">
                                <strong>Feedback:</strong> {{ $pengumpulan->feedback ?? '-' }}
                            </div>
                        @else
                            <span class="badge-status badge-belum">Belum</span>
                        @endif

                        <a href="{{ route('mahasiswa.kelas.tugas.show', ['kelas' => $tgs->kelas_id, 'tugas' => $tgs->id]) }}" class="btn-lihat">
                            Lihat {{ ucfirst($currentTipe) }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">
            Belum ada {{ $currentTipe === 'tugas' ? 'tugas' : 'ujian' }} di kelas ini.
        </div>
    @endif
</div>
@endsection
