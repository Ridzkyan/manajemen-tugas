@extends('layouts.dosen')

@section('content')
<link href="{{ asset('css/backsite/dosen/crud.css') }}" rel="stylesheet">


<div class="page-wrapper">
    <div class="card-modern">
        <h5 class="form-title">
            <i class="bi bi-pencil-square me-2"></i> Edit Kelas
        </h5>

        @include('dosen.kelola_kelas.form', ['kelas' => $kelas])

        <div class="form-actions text-center mt-4">
            <button type="submit" form="form-kelas" class="btn btn-custom me-2">
                <i class="bi bi-save me-1"></i> Update Kelas
            </button>
            <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary-custom">
                <i class="bi bi-x-circle me-1"></i> Batal
            </a>
        </div>
    </div>
</div>
@endsection

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#008080'
    });
</script>
@endif