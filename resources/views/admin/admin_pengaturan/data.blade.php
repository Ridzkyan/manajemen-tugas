@extends('layouts.admin')

@section('title', 'Backup & Restore Data')

@section('content')
<link rel="stylesheet" href="{{ asset('css/backsite/admin/pengaturan.css') }}">

<div class="container py-5">
    <h3 class="mb-5 text-center">
        <i class="fas fa-database me-2 text-primary"></i>Backup & Restore Database
    </h3>

    <div class="row justify-content-center g-4">
        <!-- Baris 1 -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 text-center h-100">
                <div class="mb-3">
                    <i class="fas fa-download fa-2x text-success"></i>
                </div>
                <h5 class="fw-bold">Backup Database</h5>
                <p class="text-muted small">Download salinan database dalam format .sql</p>
                <form action="{{ route('admin.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success mt-2">
                        <i class="fas fa-file-download me-1"></i> Download .sql
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm p-4 text-center h-100">
                <div class="mb-3">
                    <i class="fas fa-upload fa-2x text-primary"></i>
                </div>
                <h5 class="fw-bold">Restore Database</h5>
                <p class="text-muted small">Unggah file .sql untuk mengembalikan data</p>
                <form action="{{ route('admin.restore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="sql_file" accept=".sql" class="form-control mb-2" required>
                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-upload me-1"></i> Restore File
                    </button>
                </form>
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="col-md-6">
            <div class="card shadow-sm p-4 text-center h-100">
                <div class="mb-3">
                    <i class="fas fa-code fa-2x text-info"></i>
                </div>
                <h5 class="fw-bold">Backup SQL Saja</h5>
                <p class="text-muted small">Backup hanya struktur dan data SQL tanpa file</p>
                <form action="{{ route('admin.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info text-white mt-2">
                        <i class="fas fa-code me-1"></i> Download SQL
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm p-4 text-center h-100">
                <div class="mb-3">
                    <i class="fas fa-archive fa-2x text-warning"></i>
                </div>
                <h5 class="fw-bold">Backup ZIP</h5>
                <p class="text-muted small">Backup database dan file dalam satu ZIP</p>
                <form action="{{ route('admin.backup.zip') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning text-dark mt-2">
                        <i class="fas fa-file-archive me-1"></i> Download ZIP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6'
        });
    @elseif(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endsection
