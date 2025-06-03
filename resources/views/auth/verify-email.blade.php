@extends('layouts.mahasiswa')

@section('content')
<style>
    body {
        background: linear-gradient(to right, #ffb347, #44d9f7);
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .verify-card {
        background-color: white;
        padding: 3rem 2rem;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        text-align: center;
    }

    .verify-card h4 {
        color: #008080;
    }

    .btn-primary {
        background-color: #008080;
        border: none;
    }
</style>

<div class="verify-card">
    <h4>Verifikasi Email Kamu</h4>
    <p>Silakan cek email kamu dan klik link verifikasi yang telah dikirim.</p>

    @if (session('message'))
        <div class="alert alert-success mt-3">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('mahasiswa.email-verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-primary mt-3">Kirim Ulang Email Verifikasi</button>
    </form>
</div>
@endsection
