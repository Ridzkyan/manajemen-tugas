@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/backsite/admin/profil.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="profil-wrapper py-5">
    <div class="card-profile">
        <h4><i class="fas fa-key"></i> Ganti Password</h4>
        <form id="passwordForm" action="{{ route('admin.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3 text-start">
                <label for="current_password">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>

            <div class="form-group mb-3 text-start">
                <label for="new_password">Password Baru</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="form-group mb-3 text-start">
                <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" name="new_password_confirmation" class="form-control" required>
            </div>

            <button type="button" class="btn" id="saveBtn">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    });
</script>
@endif

<script>
    document.getElementById('saveBtn').addEventListener('click', function () {
        Swal.fire({
            title: 'Yakin ingin mengganti password?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ganti',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#008080',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('passwordForm').submit();
            }
        });
    });
</script>
@endsection
