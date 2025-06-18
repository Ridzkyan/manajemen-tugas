@extends('layouts.mahasiswa')

@section('page_title', 'Pengaturan Mahasiswa')

@section('content')

{{-- Import CSS khusus halaman komunikasi mahasiswa --}}
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/komunikasi.css') }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- SweetAlert --}}
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

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
        showConfirmButton: true
    });
</script>
@endif

<div class="pengaturan-wrapper">
    <h2 class="pengaturan-title">
        <i class="bi bi-chat-dots-fill text-warning me-2"></i> Komunikasi Kelas
    </h2>

    <div class="row">
        @forelse($kelasmahasiswa as $kelas)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card-komunikasi h-100">
                    <h5>{{ $kelas->nama_kelas }}</h5>
                    <p><strong>Mata Kuliah:</strong> {{ $kelas->nama_matakuliah }}</p>

                    @if($kelas->whatsapp_link)
                        <a href="{{ $kelas->whatsapp_link }}" target="_blank" class="btn-wa">
                            <i class="bi bi-whatsapp me-1"></i> Masuk Grup WA
                        </a>
                    @else
                        <button class="btn-disabled" disabled>
                            <i class="bi bi-ban me-1"></i> Tidak ada link WA
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center">Kamu belum tergabung di kelas manapun.</p>
        @endforelse
    </div>
</div>
@endsection
