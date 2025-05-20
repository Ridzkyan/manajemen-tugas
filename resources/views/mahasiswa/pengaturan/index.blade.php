@extends('layouts.mahasiswa')

@section('title', 'Pengaturan')

@section('content')
<div class="container">
    <h4 class="mb-4">Pengaturan Akun</h4>

    <ul class="list-group">
        <li class="list-group-item">
            <a href="{{ route('mahasiswa.profile-edit.edit') }}">✏️ Edit Profil</a>
            
        <li class="list-group-item">
            <a href="{{ route('mahasiswa.password-edit.edit') }}">🔒 Ganti Password</a>
        </li>
        <li class="list-group-item">
            <form method="POST" action="{{ route('mahasiswa.logout') }}" onsubmit="return confirm('Yakin ingin logout?')">
                @csrf
                <button type="submit" class="btn btn-link text-danger p-0">🚪 Logout</button>
            </form>
        </li>
    </ul>
</div>
@endsection
