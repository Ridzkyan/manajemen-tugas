@extends('layouts.dosen')

@section('content')
<style>
    body {
        background-color: #FFF9F3;
        margin: 0;
        padding: 0;
    }

    .container-wrapper {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 16px;
    }

    .title-header {
        font-size: 1.8rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .title-header i {
        color: #f4c430;
        font-size: 1.6rem;
    }

    .empty-message {
        background: #fff;
        border-radius: 12px;
        padding: 32px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        color: #666;
        font-size: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin-top: 32px; /* ✅ Tambahan jarak */
    }

    .empty-message i {
        font-size: 2rem;
        color: #999;
    }

    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }

    .table-modern th {
        text-align: left;
        padding: 12px 16px;
        font-size: 14px;
        color: #666;
    }

    .table-modern td {
        background: #fff;
        padding: 16px;
        border-radius: 8px;
        font-size: 15px;
        color: #333;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        vertical-align: middle;
    }

    .table-modern td.name {
        font-weight: 600;
        color: #008080;
    }
</style>

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