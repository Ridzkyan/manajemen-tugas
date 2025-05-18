@extends('layouts.dosen')

@section('content')
<style>
    .page-wrapper {
        height: 100%;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #FFF9F3;
    }

    .card-modern {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 80px;
        width: 100%;
        max-width: 1200px;
        animation: fadeIn 0.5s ease-in-out;
    }

    .form-title {
        text-align: center;
        font-weight: 600;
        color: #008080;
        margin-bottom: 24px;
    }

    .form-control {
        padding: 12px 16px;
        font-size: 16px;
        border-radius: 8px;
    }

    .btn-custom {
        background-color: #008080;
        color: #fff;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #F9A825;
        color: #fff;
    }

    .btn-secondary-custom {
        background-color: #6c757d;
        color: #fff;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .btn-secondary-custom:hover {
        background-color: #F9A825;
        color: #fff;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="page-wrapper">
    <div class="card-modern">
        <h5 class="form-title">
            <i class="bi bi-pencil-square me-2"></i> Edit Kelas
        </h5>

        @include('dosen.kelola_kelas.form', ['kelas' => $kelas])

        <div class="form-actions text-center mt-4">
            <button type="submit" form="form-kelas" class="btn btn-custom me-2">
                <i class="bi bi-save me-1"></i> Update Kelas
            </button>
            <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary-custom">
                <i class="bi bi-x-circle me-1"></i> Batal
            </a>
        </div>
    </div>
</div>
@endsection

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#008080'
    });
</script>
@endif