@extends('layouts.dosen')

@section('content')
<div class="container">
    <h4 class="mb-4">Rekap Nilai</h4>

    {{-- Pilih Kelas --}}
    <form method="GET" action="{{ route('dosen.rekap_nilai.index') }}" class="mb-3">
        <div class="input-group">
            <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" {{ $selectedKelasId == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }} ({{ $kelas->nama_matakuliah }})
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if($tugas->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Judul Tugas</th>
                    <th>Nilai</th>
                    <th>Feedback</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tugas as $tgs)
                    <tr>
                        <td>{{ $tgs->judul }}</td>
                        <td>{{ $tgs->nilai ?? 'Belum dinilai' }}</td>
                        <td>{{ $tgs->feedback ?? '-' }}</td>
                        <td>{{ $tgs->deadline ?? '-' }}</td>
                    </tr>
                @endforeach
                @if($selectedKelasId)
    <a href="{{ route('dosen.rekap_nilai.export', $selectedKelasId) }}" class="btn btn-success mb-3">
        📥 Export Excel
    </a>
@endif

            </tbody>
        </table>
    </div>
    @else
        <div class="alert alert-info">Silakan pilih kelas untuk melihat rekap nilai.</div>
    @endif
</div>
@endsection
