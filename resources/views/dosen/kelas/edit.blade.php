@extends('layouts.dosen')

@section('content')
<div class="container">
    <h3>Edit Kelas</h3>

    <form method="POST" action="{{ route('dosen.kelas.update', $kelas->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama_kelas" class="form-label">Nama Kelas</label>
            <input type="text" id="nama_kelas" name="nama_kelas" class="form-control" value="{{ $kelas->nama_kelas }}" required>
        </div>

        <div class="mb-3">
            <label for="nama_matakuliah" class="form-label">Nama Mata Kuliah</label>
            <input type="text" id="nama_matakuliah" name="nama_matakuliah" class="form-control" value="{{ $kelas->nama_matakuliah }}" required>
        </div>

        <div class="mb-3">
            <label for="whatsapp_link" class="form-label">Link WhatsApp</label>
            <input type="url" id="whatsapp_link" name="whatsapp_link" class="form-control" value="{{ $kelas->whatsapp_link }}" placeholder="https://wa.me/xxxxxx" />
        </div>

        <button type="submit" class="btn btn-primary">Update Kelas</button>
        <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
