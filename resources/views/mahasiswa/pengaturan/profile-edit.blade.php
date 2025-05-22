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

<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <style>
        .profil-card {
            width: 100%;
            max-width: 600px;
            border-radius: 20px;
            background-color: #ffffff;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .profil-title {
            color: #000000;
            font-weight: bold;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .input-custom {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 15px;
            border: 1px solid #ced4da;
        }

        input[type="file"] {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 8px;
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

        .btn-kembali {
            background-color: #e0e0e0;
            color: #333;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 12px 28px;
            transition: 0.3s ease;
        }

        .btn-kembali:hover {
            background-color: #f5a04e;
            color: white;
        }

        .label-primary {
            color: #000000;
            font-weight: 600;
        }

        .icon-orange {
            color: #f5a04e;
            margin-right: 6px;
        }

        #foto-preview {
            max-width: 150px;
            max-height: 150px;
            margin-top: 15px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
    </style>

    <div class="card profil-card">
        <div class="card-body">
            <h4 class="profil-title">
                <i class="fas fa-user-circle icon-orange me-2"></i> Ubah Profil Mahasiswa
            </h4>

            <form method="POST" action="{{ route('mahasiswa.profile-edit.update') }}" enctype="multipart/form-data">
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
