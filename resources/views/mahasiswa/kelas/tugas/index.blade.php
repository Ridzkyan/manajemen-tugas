@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tugas/Ujian Kelas: {{ $kelas->nama_kelas }}</h3>

    @if($tugas->count() > 0)
        <ul>
            @foreach($tugas as $tgs)
                <li>
                    {{ $tgs->judul }} ({{ ucfirst($tgs->tipe) }}) - Deadline: {{ $tgs->deadline ?? '-' }}
                    @if($tgs->file_soal)
                        <a href="{{ asset('storage/' . $tgs->file_soal) }}" target="_blank">Download Soal</a>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-muted">Belum ada tugas atau ujian.</p>
    @endif
</div>
@endsection
