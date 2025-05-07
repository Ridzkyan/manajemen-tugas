@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Ubah Profil</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset($admin->foto) }}" class="img-fluid rounded" alt="Foto Profil">
                </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $admin->name }}">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $admin->email }}">
        </div>

        <div class="mb-3">
            <label>Foto Profil</label><br>
            @if($admin->foto)
            <a href="#" data-bs-toggle="modal" data-bs-target="#fotoModal">
                <img src="{{ asset($admin->foto) }}" width="80" class="mb-2 rounded-circle shadow-sm" style="cursor: zoom-in;">
            </a>
            @endif
            <input type="file" name="foto" class="form-control">
        </div>

        <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</div>
@endsection
