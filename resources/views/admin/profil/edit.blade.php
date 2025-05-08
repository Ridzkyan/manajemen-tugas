@extends('layouts.admin')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .profil-wrapper {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
    }

    .card-profile {
        width: 100%;
        max-width: 900px;
        background: #fff;
        border-radius: 16px;
        padding: 40px 50px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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
        text-align: left;
        display: block;
    }

    .form-control {
        font-size: 15px;
    }

    .profile-pic {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
        margin-bottom: 12px;
    }

    #foto {
        display: none;
    }

    #foto + label {
        cursor: pointer;
        padding: 8px 18px;
        border-radius: 8px;
        background-color: #008080;
        color: white;
        display: inline-block;
        font-weight: 500;
        transition: 0.3s ease;
        font-size: 14px;
    }

    #foto + label i {
        margin-right: 6px;
    }

    #foto + label:hover,
    #saveBtn:hover {
        background-color: #f5a04e !important;
        color: #fff !important;
        border-color: #f5a04e !important;
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
</style>

<div class="profil-wrapper">
    <div class="card-profile">
        <h4><i class="fas fa-user-edit"></i> Ubah Profil</h4>
        <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group mb-3 text-start">
                <label for="name">Nama</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
            </div>

            <div class="form-group mb-3 text-start">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
            </div>

            <div class="form-group text-center mb-3">
                <label for="foto">Foto Profil</label><br>
                <img src="{{ asset($admin->foto) }}" class="profile-pic mb-2" style="cursor: zoom-in;">
                <br>
                <input type="file" name="foto" id="foto" accept="image/*">
                <label for="foto"><i class="fas fa-camera"></i> Ganti Foto</label>
            </div>

            <button type="submit" class="btn" id="saveBtn">
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
@endsection