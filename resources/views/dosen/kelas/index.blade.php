@extends('layouts.dosen')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold">Daftar Kelas Saya</h4>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tombol Tambah Kelas --}}
    <a href="{{ route('dosen.kelas.create') }}" class="btn btn-primary mb-3">+ Tambah Kelas</a>

    {{-- Tabel Daftar Kelas --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped table-hover mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Mata Kuliah</th>
                        <th>Kode Unik</th>
                        <th>Grup WhatsApp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse($kelas as $kls)
                        <tr>
                            <td>{{ $kls->nama_kelas }}</td>
                            <td>{{ $kls->nama_matakuliah }}</td>
                            <td>
                                {{ $kls->kode_unik }}
                                <button type="button" class="btn btn-sm btn-outline-secondary copy-btn ms-2" data-code="{{ $kls->kode_unik }}">
                                    📋 Salin
                                </button>
                            </td>
                            <td>
                                @if($kls->whatsapp_link)
                                    <a href="{{ $kls->whatsapp_link }}" class="btn btn-sm btn-success" target="_blank">Join WhatsApp</a>
                                @else
                                    <span class="text-muted">Tidak ada link</span>
                                @endif
                            </td>
                            <td class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="{{ route('dosen.kelas.show', $kls->id) }}" class="btn btn-sm btn-info text-white">👥 Mahasiswa</a>
                                <a href="{{ route('dosen.kelas.edit', $kls->id) }}" class="btn btn-sm btn-warning text-white">✏️ Edit</a>
                                <form action="{{ route('dosen.kelas.destroy', $kls->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">🗑 Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted">Belum ada kelas yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const code = this.dataset.code;
            navigator.clipboard.writeText(code);
            alert('Kode disalin: ' + code);
        });
    });
</script>
@endsection
