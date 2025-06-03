@extends('layouts.mahasiswa')

@section('title', 'Tugas & Ujian')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ session('error') }}',
            showConfirmButton: true
        });
    </script>
@endif

<style>
    .card-tugas {
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        padding: 20px;
        height: 100%;
        transition: 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-tugas:hover {
        transform: scale(1.02);
    }

    .btn-lihat {
        background-color: #f5a04e;
        color: #fff;
        border-radius: 20px;
        padding: 8px 16px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s ease;
        margin-top: 15px;
    }

    .btn-lihat:hover {
        background-color: #d88c2f;
        color: #fff;
        text-decoration: none;
    }

    .tugas-header {
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 8px;
        color: black;
    }

    .tugas-info {
        font-size: 14px;
        margin-bottom: 4px;
        color: black;
    }

    .badge-status {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 15px;
        color: white;
        font-weight: 600;
        align-self: flex-start;
    }

    .badge-terkumpul {
        background-color: #28a745;
    }

    .badge-belum {
        background-color: #008080;
    }
</style>

<div class="container py-3">
    <h3 class="mb-4 text-center">📋 Daftar Tugas & Ujian</h3>

    @php
        $pengumpulanTugasIds = $pengumpulanTugas->pluck('tugas_id')->toArray();
    @endphp

    @if($tugas->count())
        <div class="row">
            @foreach($tugas as $tgs)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card-tugas h-100">
                        <div class="tugas-header">{{ $tgs->judul }}</div>
                        <div class="tugas-info"><strong>Kelas:</strong> {{ $tgs->kelas->nama_matakuliah ?? '-' }}</div>
                        <div class="tugas-info"><strong>Deskripsi:</strong> {{ $tgs->deskripsi ?? '-' }}</div>
                        <div class="tugas-info">
                            <strong>Deadline:</strong> 
                            {{ \Carbon\Carbon::parse($tgs->deadline)->format('d M Y - H:i') }}
                        </div>

                        @if(in_array($tgs->id, $pengumpulanTugasIds))
                            <span class="badge-status badge-terkumpul">Terkumpul</span>
                        @else
                            <span class="badge-status badge-belum">Belum</span>
                        @endif

                        <a href="{{ route('mahasiswa.kelas.tugas.show', ['kelas' => $tgs->kelas_id, 'tugas' => $tgs->id]) }}" class="btn-lihat">
                            Lihat Tugas
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Belum ada tugas di kelas ini.</div>
    @endif
</div>

@endsection
