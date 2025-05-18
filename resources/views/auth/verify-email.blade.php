@extends('layouts.mahasiswa')

@section('content')
<div class="container text-center">
    <h4>Verifikasi Email Kamu</h4>
    <p>Silakan cek email kamu dan klik link verifikasi yang telah dikirim.</p>

    @if (session('message'))
        <div class="alert alert-success mt-3">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary mt-3">Kirim Ulang Email Verifikasi</button>
    </form>
</div>
@endsection
