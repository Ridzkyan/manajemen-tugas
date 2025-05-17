@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">
    <div class="bg-white p-4 rounded shadow" style="border-left: 6px solid #008080;">
        <h4 class="fw-bold mb-4 text-dark">➕ Tambah User Baru</h4>

        <form id="formUser" method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Nama --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nama</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            {{-- Role --}}
            <div class="mb-3">
                <label for="role" class="form-label fw-semibold">Role</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>

            {{-- Kode Unik --}}
            <div class="mb-3" id="kodeUnikField" style="display: none;">
                <label for="kode_unik" class="form-label fw-semibold">Kode Unik Dosen</label>
                <input type="text" name="kode_unik" id="kode_unik" class="form-control" readonly>
            </div>

            <button type="submit" class="btn text-white me-2" style="background-color: #008080;">Simpan</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
    // Kode Unik logic
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const kodeUnikField = document.getElementById('kodeUnikField');
        const kodeUnikInput = document.getElementById('kode_unik');

        function generateKodeUnik() {
            return 'DSN-' + Math.floor(10000 + Math.random() * 90000);
        }

        function toggleKodeUnikField() {
            if (roleSelect.value === 'dosen') {
                kodeUnikField.style.display = 'block';
                kodeUnikInput.value = generateKodeUnik();
            } else {
                kodeUnikField.style.display = 'none';
                kodeUnikInput.value = '';
            }
        }

        roleSelect.addEventListener('change', toggleKodeUnikField);
        toggleKodeUnikField();
    });

    // SweetAlert2 Konfirmasi Submit
    const form = document.getElementById('formUser');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Tambahkan User?',
            text: "Pastikan data sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#008080',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, tambahkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endsection