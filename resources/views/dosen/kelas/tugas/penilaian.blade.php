@extends('layouts.dosen')

@section('content')
<div class="container">
    <h3>Penilaian Tugas: {{ $tugas->judul }}</h3>

    <form method="POST" action="{{ route('dosen.tugas.nilai', ['kelas' => $kelas->id, 'tugas' => $tugas->id]) }}">
        @csrf

        <div class="mb-3">
            <label>Nilai</label>
            <input type="number" name="nilai" class="form-control" required min="0" max="100">
        </div>

        <div class="mb-3">
            <label>Feedback</label>
            <textarea name="feedback" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Berikan Nilai</button>
    </form>

    <h4>Mahasiswa yang Mengumpulkan Tugas:</h4>
    <ul>
        @foreach($mahasiswa as $mhs)
            <li>{{ $mhs->nama }} - {{ $mhs->pivot->created_at }} </li>
        @endforeach
    </ul>
</div>
@endsection
