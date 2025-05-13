@extends('layouts.dosen')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            Buat Kelas Baru
        </div>
        <div class="card-body">
            {{-- Include form reusable --}}
            @include('dosen.kelola_kelas.form')
        </div>
    </div>
</div>
@endsection
