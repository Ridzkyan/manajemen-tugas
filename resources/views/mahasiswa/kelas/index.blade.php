@extends('layouts.mahasiswa')
@section('title', 'Kelas Saya')

@section('content')
<style>
    .card-kelas {
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 20px;
        height: 100%;
        transition: 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-kelas:hover {
        transform: scale(1.01);
    }

    .btn-masuk,
    .btn-keluar {
        height: 44px;
        font-size: 16px;
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-masuk {
        background-color: #f5a04e;
        color: #fff;
    }

    .btn-masuk:hover {
        background-color: #d88c2f;
    }

    .btn-keluar {
        background-color: #008080;
        color: #fff;
        border: none;
    }

    .btn-keluar:hover {
        background-color: #006666;
    }

    .kelas-header {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 8px;
        color: #000;
    }

    .kelas-sub {
        margin-bottom: 8px;
        color: #333;
        font-size: 14px;
    }

    .btn-group {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-3">
    <h3 class="mb-4 text-center">📚 Daftar Kelas yang Kamu Ikuti</h3>

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
                                    <i class="fas fa-sign-in-alt"></i> Masuk Kelas
                                </button>
                            </form>

                            <!-- Tombol Keluar dengan SweetAlert -->
                            <form action="{{ route('mahasiswa.kelas.leave', $kelas->id) }}" method="POST" class="form-keluar flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-keluar w-100 tombol-keluar">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">Kamu belum bergabung dengan kelas manapun.</div>
        <div class="text-center mt-3">
            <a href="{{ route('mahasiswa.join.index') }}" class="btn btn-success">🔑 Gabung ke Kelas</a>
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
