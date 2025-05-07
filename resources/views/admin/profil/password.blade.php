@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Ganti Password</h4>
    <form action="{{ route('admin.password.update') }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Password Saat Ini</label>
            <input type="password" name="current_password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Password Baru</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <button class="btn btn-warning">Ubah Password</button>
    </form>
</div>
@endsection
