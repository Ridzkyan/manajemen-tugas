@extends('layouts.mahasiswa')

@section('page_title', 'Pengaturan Mahasiswa')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .pengaturan-wrapper {
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
        padding: 40px 20px 60px;
    }

    .pengaturan-title {
        font-size: 2.3rem;
        font-weight: 700;
        color: #333;
        text-align: center;
        margin-bottom: 30px;
    }

    .card-komunikasi {
        border-radius: 16px;
        background-color: #fff;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        transition: 0.3s ease;
        padding: 24px;
    }

    .card-komunikasi:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .card-komunikasi h5 {
        color: #008080;
        font-weight: 700;
    }

    .card-komunikasi p {
        margin-bottom: 16px;
        font-size: 14px;
        color: #555;
    }

    .btn-wa {
        background-color: #25D366;
        border: none;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 12px;
        color: white;
        transition: 0.3s ease;
    }

    .btn-wa:hover {
        background-color: #1cb255;
        color: white;
    }

    .btn-disabled {
        background-color: #adb5bd;
        cursor: not-allowed;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        color: white;
        border: none;
    }
</style>

{{-- SweetAlert --}}
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

<div class="pengaturan-wrapper">
    <h2 class="pengaturan-title">
        <i class="bi bi-chat-dots-fill text-warning me-2"></i> Komunikasi Kelas
    </h2>

    <div class="row">
        @forelse($kelasmahasiswa as $kelas)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card-komunikasi h-100">
                    <h5>{{ $kelas->nama_kelas }}</h5>
                    <p><strong>Mata Kuliah:</strong> {{ $kelas->nama_matakuliah }}</p>

                    @if($kelas->whatsapp_link)
                        <a href="{{ $kelas->whatsapp_link }}" target="_blank" class="btn-wa">
                            <i class="bi bi-whatsapp me-1"></i> Masuk Grup WA
                        </a>
                    @else
                        <button class="btn-disabled" disabled>
                            <i class="bi bi-ban me-1"></i> Tidak ada link WA
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center">Kamu belum tergabung di kelas manapun.</p>
        @endforelse
    </div>
</div>
@endsection
