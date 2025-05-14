@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h4>Verifikasi Email Kamu</h4>
    <p>Silakan cek email dan klik link verifikasi.</p>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Kirim Ulang Email Verifikasi</button>
    </form>
</div>
@endsection
