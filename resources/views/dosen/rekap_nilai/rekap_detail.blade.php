@extends('layouts.dosen')

@section('content')
<div class="container">
    <h4 class="mb-4">Rekap Nilai: {{ $kelas->nama_matakuliah }} - {{ $kelas->nama_kelas }}</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Judul Tugas</th>
                <th>Nilai</th>
                <th>Feedback</th>
                <th>Deadline</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tugas as $tgs)
                <tr>
                    <td>{{ $tgs->judul }}</td>
                    <td>{{ $tgs->nilai ?? 'Belum dinilai' }}</td>
                    <td>{{ $tgs->feedback ?? '-' }}</td>
                    <td>{{ $tgs->deadline ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted text-center">Belum ada tugas di kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
