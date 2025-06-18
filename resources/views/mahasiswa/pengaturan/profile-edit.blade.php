@extends('layouts.mahasiswa')

@section('title', 'Edit Profil')

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
            title: 'Oops...',
            text: '{{ $errors->first() }}',
            showConfirmButton: true
        });
    </script>
@endif

<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/profile_edit.css') }}">

<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card profil-card">
        <div class="card-body">
            <h4 class="profil-title">
                <i class="fas fa-user-circle icon-orange me-2"></i> Ubah Profil Mahasiswa
            </h4>

            <form method="POST" action="{{ route('mahasiswa.pengaturan.profile.edit') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="form-label label-primary">
                        <i class="fas fa-user icon-orange"></i> Nama Mahasiswa
                    </label>
                    <input type="text" name="name" class="form-control input-custom" value="{{ old('name', $mahasiswa->name) }}" required>
                    @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label label-primary">
                        <i class="fas fa-envelope icon-orange"></i> Email Mahasiswa
                    </label>
                    <input type="email" name="email" class="form-control input-custom" value="{{ old('email', $mahasiswa->email) }}" required>
                    @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label label-primary">
                        <i class="fas fa-image icon-orange"></i> Foto Profil
                    </label>
                    <input type="file" name="foto" class="form-control input-custom" accept="image/*" onchange="previewFoto(event)">
                    @error('foto') <div class="text-danger mt-1">{{ $message }}</div> @enderror

                    <img 
                        id="foto-preview" 
                        src="{{ $mahasiswa->foto ? asset($mahasiswa->foto) : asset('images/default-profile.png') }}" 
                        alt="Preview Foto Profil">
                </div>

                <div class="d-flex mt-4">
                    <button type="submit" class="btn btn-simpan me-2">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('mahasiswa.pengaturan.index') }}" class="btn btn-kembali">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewFoto(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('foto-preview');
            output.src = reader.result;
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>

@endsection
