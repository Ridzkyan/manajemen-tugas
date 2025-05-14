@extends('layouts.dosen')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            Edit Kelas
        </div>
        <div class="card-body">
            {{-- Include form reusable dan kirim data kelas --}}
            @include('dosen.kelola_kelas.edit', ['kelas' => $kelas])
            
            {{-- Tombol batal di luar form jika ingin terpisah --}}
            <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary mt-2">Batal</a>
        </div>
    </div>
</div>
@endsection
