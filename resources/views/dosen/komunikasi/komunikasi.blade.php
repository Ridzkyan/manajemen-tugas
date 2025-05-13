@extends('layouts.dosen')

@section('content')
<div class="container">
    <h3>Grup WhatsApp Kelas</h3>
    <ul class="list-group mt-3">
        @forelse($kelas as $kls)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $kls->nama_kelas }}</strong> - {{ $kls->nama_matakuliah }}
                </div>
                <div>
                    @if($kls->whatsapp_link)
                        <a href="{{ $kls->whatsapp_link }}" target="_blank" class="btn btn-success btn-sm">Join WhatsApp</a>
                    @else
                        <span class="text-muted">Tidak ada link</span>
                    @endif
                </div>
            </li>
        @empty
            <li class="list-group-item">Belum ada kelas atau link WhatsApp.</li>
        @endforelse
    </ul>
</div>
@endsection
