@extends('layouts.mahasiswa')

@section('page_title', 'Pengaturan Mahasiswa')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .pengaturan-wrapper {
        max-width: 700px;
        margin: 0 auto;
        padding: 50px 30px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }

    .pengaturan-title {
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #444;
    }

    .pengaturan-subtitle {
        text-align: center;
        color: #777;
        margin-bottom: 40px;
    }

    .pengaturan-card {
        border-radius: 20px;
        transition: 0.3s ease;
        padding: 40px 25px;
        text-align: center;
        background-color: #ffffff;
        border: 1px solid #e3e3e3;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .pengaturan-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.07);
    }

    .pengaturan-icon {
        font-size: 3rem;
        margin-bottom: 16px;
    }

    .badge-custom {
        padding: 8px 16px;
        font-size: 0.9rem;
        border-radius: 12px;
    }

    .btn-primary {
        background-color: #008080; /* warna toska */
        border: none;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #006666;
        transform: translateY(-1px);
        color: white;
    }

    @media (max-width: 768px) {
        .pengaturan-title {
            font-size: 1.8rem;
        }

        .pengaturan-card {
            padding: 30px 20px;
        }

        .pengaturan-icon {
            font-size: 2.4rem;
        }
    }
</style>

{{-- Flash message SweetAlert --}}
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


    <h2 class="pengaturan-title">
        <i class="bi bi-gear-fill text-warning me-2"></i> 🔑 Gabung ke Kelas
    </h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form Input Kode Unik --}}
    <form method="POST" action="{{ route('mahasiswa.join.store') }}">
        @csrf

        <div class="pengaturan-wrapper">
        <div class="mb-3">
            <label for="kode_unik" class="form-label">Masukkan Kode Unik Kelas</label>
            <input type="text" id="kode_unik" name="kode_unik" class="form-control" placeholder="KLS- " required>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Gabung Kelas</button>
            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">⬅️ Kembali ke Dashboard</a>
        </div>
    </form>
</div>
@endsection


