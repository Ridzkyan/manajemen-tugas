@extends('layouts.mahasiswa')

@section('title', 'Materi Kelas')

@section('content')
<style>
    .materi-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 20px;
    }

    .materi-title {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 25px;
        text-align: center;
        color: #333;
    }

    .materi-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 16px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.03);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .materi-judul {
        font-weight: 500;
        color: #333;
    }

    .btn-materi {
        border-radius: 10px;
        padding: 6px 14px;
        font-size: 14px;
        font-weight: 500;
    }

    .btn-outline-primary:hover,
    .btn-outline-info:hover {
        opacity: 0.9;
    }

    .no-materi {
        font-style: italic;
        color: #777;
        text-align: center;
        margin-top: 30px;
    }
</style>

<div class="materi-wrapper">
    <div class="materi-title">
        📚 Materi {{ $kelas->nama_kelas ?? $kelas->nama_matakuliah ?? 'Nama Kelas' }}
    </div>

    @if($materis->count() > 0)
        @foreach($materis as $materi)
            <div class="materi-card">
                <div class="materi-judul">{{ $materi->judul }}</div>
                <div>
                    @if($materi->tipe == 'pdf')
                        <a href="{{ asset('storage/' . $materi->file) }}" class="btn btn-outline-primary btn-materi" target="_blank">
                            📄 Lihat PDF
                        </a>
                    @elseif($materi->tipe == 'video' || $materi->link)
                        <a href="{{ $materi->link }}" class="btn btn-outline-info btn-materi" target="_blank">
                            ▶ Video
                        </a>
                    @else
                        <span class="text-muted small">❌ Tidak tersedia</span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="no-materi">Belum ada materi di kelas ini.</div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('mahasiswa.kelas.show', $kelas->id) }}" class="btn btn-secondary">
            ⬅ Kembali ke Kelas
        </a>
    </div>
</div>
@endsection
