@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        Buat Kelas Baru
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('dosen.kelas.store') }}">
            @csrf
            <div class="mb-3">
                <label for="nama_kelas" class="form-label">Nama Kelas</label>
                <input type="text" id="nama_kelas" name="nama_kelas" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nama_matakuliah" class="form-label">Nama Mata Kuliah</label>
                <input type="text" id="nama_matakuliah" name="nama_matakuliah" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="whatsapp_link" class="form-label">Link Grup WhatsApp</label>
                <input type="url" id="whatsapp_link" name="whatsapp_link" class="form-control" placeholder="https://wa.me/xxxxxxxxxx">
            </div>
            <button type="submit" class="btn btn-primary">Buat Kelas</button>
        </form>
    </div>
</div>

@endsection
