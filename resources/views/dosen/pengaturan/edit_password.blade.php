@extends('layouts.dosen')

@section('title', 'Ubah Password')

@section('content')
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
            title: 'Gagal!',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#008080'
        });
    </script>
@endif


<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <style>
        .password-card {
            width: 100%;
            max-width: 700px;
            border-radius: 20px;
            border: none;
            background-color: #ffffff;
            padding: 40px 48px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .password-title {
            color: #000000;
            font-weight: bold;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .input-custom {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 16px;
            border: 1px solid #ced4da;
        }

        .btn-simpan {
            background-color: #00838f;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 12px 28px;
            transition: 0.3s ease;
        }

        .btn-simpan:hover {
            background-color: #f5a04e;
        }

        .label-primary {
            color: #000000;
            font-weight: 600;
        }

        .icon-orange {
            color: #f5a04e;
            margin-right: 6px;
        }
    </style>

    <div class="card password-card">
        <div class="card-body">
            <h4 class="password-title">
                <i class="fas fa-lock icon-orange me-2"></i> Ubah Password
            </h4>

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

            <form id="passwordForm" action="{{ route('dosen.pengaturan.password.update') }}" method="POST">
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
                    <input type="password" name="new_password" class="form-control input-custom" required>
                </div>

                <div class="mb-3">
                    <label class="form-label label-primary">
                        <i class="fas fa-lock icon-orange"></i> Konfirmasi Password Baru
                    </label>
                    <input type="password" name="new_password_confirmation" class="form-control input-custom" required>
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