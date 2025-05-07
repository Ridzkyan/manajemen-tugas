@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .custom-alert {
        padding: 15px 20px;
        border-left: 6px solid #28a745;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #d4edda;
        color: #155724;
        animation: fadeSlideDown 0.4s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        position: relative;
    }

    @keyframes fadeSlideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-alert .btn-close {
        position: absolute;
        top: 10px;
        right: 15px;
    }
</style>

<div class="bg-white p-4 rounded shadow" style="border-left: 6px solid #008080;">
    <h4 class="fw-bold mb-4 text-dark">👥 Manajemen Pengguna</h4>

    {{-- Tombol Tambah User --}}
    <a href="{{ route('admin.create') }}" class="btn mb-3 text-white" style="background-color: #008080;">
        + Tambah User
    </a>

    {{-- Alert Sukses --}}
    @if(session('success'))
        <div class="custom-alert" id="success-alert">
            <span><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Tabel Users --}}
    <div class="table-responsive rounded shadow-sm">
        <table class="table table-bordered text-center mb-0">
            <thead style="background-color: #008080; color: white;">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Kode Unik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr style="background-color: #fef9f4;">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge" style="background-color: #f5a04e;">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        @if($user->role === 'dosen')
                            {{ $user->kode_unik }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        {{-- Tombol Edit --}}
                        <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-sm text-white" style="background-color: #ffc107;">
                            Edit
                        </a>

                        {{-- Tombol Hapus --}}
                        <button type="button" class="btn btn-sm text-white" style="background-color: #dc3545;"
                            onclick="confirmDelete('{{ route('admin.destroy', $user->id) }}')">Hapus</button>

                        {{-- Tombol Reset Password --}}
                        <button type="button" class="btn btn-sm text-white" style="background-color: #343a40;"
                            onclick="confirmReset('{{ route('admin.reset.password', $user->id) }}')">Reset PW</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    // Auto dismiss alert after 1 second
    document.addEventListener('DOMContentLoaded', function () {
        const alert = document.getElementById('success-alert');
        if (alert) {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300);
            }, 1000);
        }
    });

    function confirmDelete(url) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data tidak bisa dikembalikan setelah dihapus.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmReset(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: "Password akan direset ke default (misal: 12345678).",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#343a40',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, reset!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'PUT';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

@endsection