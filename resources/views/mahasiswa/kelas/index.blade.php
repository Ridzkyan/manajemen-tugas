@extends('layouts.mahasiswa')
@section('title', 'Kelas Saya')

@section('content')

{{-- Import CSS khusus halaman daftar kelas mahasiswa --}}
<link rel="stylesheet" href="{{ asset('css/backsite/mahasiswa/kelas_daftar.css') }}">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-3">
    <h3 class="mb-4 text-center">
        <i class="bi bi-journal-bookmark-fill icon-f5a04e me-2"></i>
        Daftar Kelas yang Kamu Ikuti
    </h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($kelasmahasiswa->count())
        <div class="row">
            @foreach($kelasmahasiswa as $kelas)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card-kelas h-100">
                        <div>
                            <div class="kelas-header">{{ $kelas->nama_matakuliah }}</div>
                            <div class="kelas-sub"><strong>Kode:</strong> {{ $kelas->kode_unik }}</div>
                            <div class="kelas-sub"><strong>Dosen:</strong> {{ $kelas->dosen->name ?? 'Tidak diketahui' }}</div>
                        </div>
                        <div class="d-flex gap-2 mt-3 align-items-center">
                            <form action="{{ route('mahasiswa.kelas.show', $kelas->id) }}" method="GET" class="flex-fill">
                                <button type="submit" class="btn btn-masuk w-100">
                                    <i class="bi bi-box-arrow-in-right"></i> Masuk Kelas
                                </button>
                            </form>
                            <!-- Tombol Keluar dengan SweetAlert -->
                            <form action="{{ route('mahasiswa.kelas.leave', $kelas->id) }}" method="POST" class="form-keluar flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-keluar w-100 tombol-keluar">
                                    <i class="bi bi-door-open-fill"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">
            Kamu belum bergabung dengan kelas manapun.
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('mahasiswa.join.index') }}" class="btn btn-success">
                <i class="bi bi-person-plus-fill"></i> Gabung ke Kelas
            </a>
        </div>
    @endif
</div>

<!-- Script SweetAlert2 -->
<script>
    document.querySelectorAll('.form-keluar').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin keluar dari kelas ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection
