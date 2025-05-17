@extends('layouts.dosen')

@section('content')

<style>
    * {
        box-sizing: border-box;
    }

    html, body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        background-color: #FFF9F3;
        overflow: hidden;
    }

    main {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
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
        font-size: 1.25rem;
    }

    .form-control {
        padding: 12px 16px;
        font-size: 16px;
        border-radius: 8px;
    }

    .form-control:focus {
        border-color: #008080;
        box-shadow: 0 0 0 0.2rem rgba(0, 128, 128, 0.2);
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

<main>
    <div class="card-modern">
        <h5 class="form-title">
            <i class="bi bi-plus-circle me-2"></i> Buat Kelas Baru
        </h5>
        @include('dosen.kelola_kelas.form')
    </div>
</main>

@endsection