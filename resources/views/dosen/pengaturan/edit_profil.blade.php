@extends('layouts.dosen')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <style>
        .profil-card {
            width: 100%;
            max-width: 1000px;
            border-radius: 20px;
            border: none;
            background-color: #ffffff;
            padding: 40px 48px;
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
    </style>
    
    <div class="card profil-card">
        <div class="card-body">
            <h4 class="profil-title">
                <i class="fas fa-user-circle icon-orange me-2"></i> Ubah Profil Dosen
            </h4>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('dosen.pengaturan.profil.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4 row">
                    <div class="col-md-6">
                        <label class="form-label label-primary">
                            <i class="fas fa-user icon-orange"></i> Nama Dosen
                        </label>
                        <input type="text" name="name" class="form-control input-custom" value="{{ old('name', $dosen->name) }}" required>
                        @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label label-primary">
                            <i class="fas fa-envelope icon-orange"></i> Email Dosen
                        </label>
                        <input type="email" name="email" class="form-control input-custom" value="{{ old('email', $dosen->email) }}" required>
                        @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label label-primary">
                        <i class="fas fa-image icon-orange"></i> Foto Profil
                    </label>
                    <input type="file" name="foto" accept="image/*" onchange="previewFoto(event)">

                    @error('foto') <div class="text-danger mt-1">{{ $message }}</div> @enderror

                    <img 
                        id="foto-preview" 
                        src="{{ $dosen->foto ? asset($dosen->foto) : asset('images/default-profile.png') }}" 
                        alt="Preview Foto Profil">
                </div>


                

                <div class="d-flex mt-4">
                    <button type="submit" class="btn btn-simpan me-2">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('dosen.pengaturan') }}" class="btn btn-kembali">
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
    reader.onload = function() {
        const output = document.getElementById('foto-preview');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection