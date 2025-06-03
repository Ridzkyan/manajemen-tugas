@extends('layouts.dosen')

@section('content')
<link href="{{ asset('css/backsite/dosen/show.css') }}" rel="stylesheet">

<div class="container-wrapper">
    <div class="title-header">
        <i class="bi bi-people-fill"></i>
        Seluruh Mahasiswa - Kelas {{ $kelas->nama_kelas }}
    </div>

    @if($mahasiswa->count() > 0)
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswa as $mhs)
                    <tr>
                        <td class="name">{{ $mhs->name }}</td>
                        <td>{{ $mhs->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-message">
            <i class="bi bi-emoji-frown"></i>
            <span>Belum ada mahasiswa yang terdaftar.</span>
        </div>
    @endif
</div>
@endsection