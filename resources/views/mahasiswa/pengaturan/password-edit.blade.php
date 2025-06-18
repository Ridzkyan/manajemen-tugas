@extends('layouts.mahasiswa')

@section('title', 'Ganti Password')

@section('content')
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/password_edit.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

@if($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '{{ $errors->first() }}',
            showConfirmButton: true
        });
    </script>
@endif

<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card password-card">
        <div class="card-body">
            @if (session('success'))
                <div id="swal-success" data-message="{{ session('success') }}"></div>
            @elseif (session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '{{ session('error') }}',
                    });
                </script>
            @endif

            <div class="container">
                <h4 class="mb-4">Ganti Password</h4>
                <form id="passwordForm" action="{{ route('mahasiswa.pengaturan.password.edit') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label label-primary">
                            <i class="fas fa-key icon-orange"></i> Password Lama
                        </label>
                        <input type="password" name="current_password" class="form-control input-custom" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label label-primary">
                            <i class="fas fa-lock icon-orange"></i> Password Baru
                        </label>
                        <input type="password" name="password" class="form-control input-custom" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label label-primary">
                            <i class="fas fa-lock icon-orange"></i> Konfirmasi Password Baru
                        </label>
                        <input type="password" name="password_confirmation" class="form-control input-custom" required>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-simpan" onclick="confirmSubmit()">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmSubmit() {
        Swal.fire({
            title: 'Yakin ingin mengubah password?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#00838f',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('passwordForm').submit();
            }
        });
    }

    // SweetAlert success setelah redirect
    document.addEventListener('DOMContentLoaded', function () {
        const successDiv = document.getElementById('swal-success');
        if (successDiv) {
            const message = successDiv.getAttribute('data-message');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
</script>
@endsection
