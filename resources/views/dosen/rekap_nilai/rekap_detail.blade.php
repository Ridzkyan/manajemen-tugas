@extends('layouts.dosen')

@section('content')
<link href="{{ asset('css/backsite/dosen/rekap-detail.css') }}" rel="stylesheet">
<div class="container">
    <h4 class="mb-4">Rekap Nilai: {{ $kelas->nama_matakuliah }} - {{ $kelas->nama_kelas }}</h4>

    @forelse($tugas as $tgs)
        <div class="mb-4">
            <h5>{{ $tgs->judul }}</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Mahasiswa</th>
                        <th>Nilai</th>
                        <th>Feedback</th>
                        <th>Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tgs->pengumpulanTugas as $pengumpulan)
                        <tr>
                            <td>{{ $pengumpulan->mahasiswa->name ?? '-' }}</td>
                            <td>{{ $pengumpulan->nilai ?? 'Belum dinilai' }}</td>
                            <td>{{ $pengumpulan->feedback ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($tgs->deadline)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted text-center">Belum ada mahasiswa mengumpulkan tugas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <div class="alert alert-info">Belum ada tugas di kelas ini.</div>
    @endforelse
</div>
@endsection
