@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('css/backsite/admin/profil.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="profil-wrapper py-5">
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
