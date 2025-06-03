@extends('layouts.dosen')

@section('content')

<link href="{{ asset('css/backsite/dosen/crud.css') }}" rel="stylesheet">


<main>
    <div class="card-modern">
        <h5 class="form-title">
            <i class="bi bi-plus-circle me-2"></i> Buat Kelas Baru
        </h5>
        @include('dosen.kelola_kelas.form')
    </div>
</main>

@endsection