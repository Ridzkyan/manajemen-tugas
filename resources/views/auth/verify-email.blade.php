@extends('layouts.mahasiswa')

@section('title', 'Verifikasi Email')

@section('content')
<link href="{{ asset('css/auth/variable.css') }}" rel="stylesheet">
<link href="{{ asset('css/auth/mahasiswa/verify.css') }}" rel="stylesheet">

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
