@extends('layouts.dosen')

@section('content')
<div class="container">
    <h3>Daftar Kelas Saya</h3>

    <a href="{{ route('dosen.kelas.create') }}" class="btn btn-primary mb-3">+ Tambah Kelas</a>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Daftar Kelas --}}
    <table class="table">
        <thead>
            <tr>
                <th>Nama Kelas</th>
                <th>Mata Kuliah</th>
                <th>Kode Unik</th>
                <th>Link WhatsApp</th>  <!-- Tambahkan kolom untuk Link WhatsApp -->
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kelas as $kls)
                <tr>
                    <td>{{ $kls->nama_kelas }}</td>
                    <td>{{ $kls->nama_matakuliah }}</td>
                    <td>{{ $kls->kode_unik }}</td>
                    <td>
                        @if($kls->whatsapp_link)  <!-- Menampilkan Link WhatsApp jika ada -->
                            <a href="{{ $kls->whatsapp_link }}" target="_blank" class="btn btn-success btn-sm">Chat di WhatsApp</a>
                        @else
                            <span class="text-muted">Tidak ada link</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            {{-- Tombol Masuk Kelas --}}
                            <a href="{{ route('dosen.kelas.manage', $kls->id) }}" class="btn btn-primary btn-sm mb-1">
                                Masuk Kelas
                            </a>

                            {{-- Tombol Lihat Mahasiswa --}}
                            <a href="{{ route('dosen.kelas.show', $kls->id) }}" class="btn btn-info btn-sm mb-1">
                                Lihat Mahasiswa
                            </a>

                            {{-- Tombol Edit --}}
                            <a href="{{ route('dosen.kelas.edit', $kls->id) }}" class="btn btn-warning btn-sm mb-1">
                                Edit
                            </a>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('dosen.kelas.destroy', $kls->id) }}" method="POST" class="d-inline form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Yakin mau hapus kelas ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
