@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .profil-wrapper {
        width: 100%;
        padding: 0 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.4s ease-in-out;
    }

    .card-profile {
        width: 100%;
        max-width: 900px;
        padding: 50px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
    }

    .card-profile h4 {
        font-weight: bold;
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
    }

    .card-profile h4 i {
        margin-right: 10px;
        color: #f5a04e;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .form-control {
        font-size: 15px;
    }

    #saveBtn {
        width: 100%;
        margin-top: 20px;
        font-weight: 600;
        padding: 12px;
        background-color: #008080;
        border: 1px solid #008080;
        color: white;
        font-size: 15px;
    }

    #saveBtn:hover {
        background-color: #f5a04e !important;
        color: #fff !important;
        border-color: #f5a04e !important;
    }
</style>

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

{{-- Alert berhasil --}}
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

{{-- Alert gagal --}}
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

{{-- Konfirmasi sebelum ganti password --}}
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