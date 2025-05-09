@extends('layouts.dosen')

@section('content')
<div class="container">
    <h3>Dashboard Dosen</h3>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tombol Tambah Kelas --}}
    <a href="{{ route('dosen.kelas.create') }}" class="btn btn-primary mb-3">+ Tambah Kelas Baru</a>

    {{-- Daftar Kelas --}}
    <div class="card">
        <div class="card-header">
            Daftar Kelas Saya
        </div>
        <div class="card-body">
            @if($kelas->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Mata Kuliah</th>
                            <th>Kode Unik</th>
                            <th>Grup WhatsApp</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <tbody>
                        @foreach($kelas as $kls)
                        <tr>
                            <td>{{ $kls->nama_kelas }}</td>
                            <td>{{ $kls->nama_matakuliah }}</td>
                            <td>
                                {{ $kls->kode_unik }}
                                <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" data-code="{{ $kls->kode_unik }}">
                                    📋 Salin
                                </button>
                            </td>
                            <td>
                                @if($kls->whatsapp_link)
                                    <a href="{{ $kls->whatsapp_link }}" target="_blank" class="btn btn-success btn-sm">Join WhatsApp</a>
                                @else
                                    <span class="text-muted">Tidak ada link</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('dosen.kelas.manage', $kls->id) }}" class="btn btn-primary btn-sm">Masuk</a>
                                    <a href="{{ route('dosen.kelas.show', $kls->id) }}" class="btn btn-info btn-sm">Lihat Mahasiswa</a>
                                    <a href="{{ route('dosen.kelas.edit', $kls->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('dosen.kelas.destroy', $kls->id) }}" method="POST" class="d-inline form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Belum ada kelas yang dibuat.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Konfirmasi hapus kelas
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin Hapus?',
                text: 'Kelas yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.copy-btn');

        buttons.forEach(btn => {
            btn.addEventListener('click', function () {
                const code = this.getAttribute('data-code');

                navigator.clipboard.writeText(code).then(() => {
                    // Notifikasi salin sukses
                    Swal.fire({
                        toast: true,
                        icon: 'success',
                        title: 'Kode disalin!',
                        text: code,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }).catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menyalin!',
                        text: 'Periksa izin clipboard browser Anda.'
                    });
                });
            });
        });
    });
</script>
@endsection
