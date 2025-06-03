@extends('layouts.dosen')

@section('content')
<link href="{{ asset('css/backsite/dosen/profil.css') }}" rel="stylesheet">
    
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