@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah User Baru</h3>

    <form method="POST" action="{{ route('admin.store') }}">
        @csrf

        {{-- Nama --}}
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        {{-- Role --}}
        <div class="mb-3">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="dosen">Dosen</option>
            </select>
        </div>

        {{-- Kode Unik --}}
        <div class="mb-3" id="kodeUnikField" style="display: none;">
            <label for="kode_unik">Kode Unik Dosen</label>
            <input type="text" name="kode_unik" id="kode_unik" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.dashboard.users') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

{{-- Script Generate Kode Unik --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const kodeUnikField = document.getElementById('kodeUnikField');
    const kodeUnikInput = document.getElementById('kode_unik');

    function generateKodeUnik() {
        const randomKode = 'DSN-' + Math.floor(10000 + Math.random() * 90000);
        return randomKode;
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

    toggleKodeUnikField();
    roleSelect.addEventListener('change', toggleKodeUnikField);
});
</script>
@endsection
