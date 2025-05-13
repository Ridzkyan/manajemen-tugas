@extends('layouts.mahasiswa')

@section('title', 'Pengaturan')

@section('content')
<div class="container">
    <h4 class="mb-4">Pengaturan Akun</h4>

    <ul class="list-group">
        <li class="list-group-item">
            <a href="{{ route('mahasiswa.profile-edit.index') }}">✏️ Edit Profil</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('mahasiswa.password-edit.index') }}">🔒 Ganti Password</a>
        </li>
        <li class="list-group-item">
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin logout?')">
                @csrf
                <button type="submit" class="btn btn-link text-danger p-0">🚪 Logout</button>
            </form>
        </li>
    </ul>
</div>
@endsection
