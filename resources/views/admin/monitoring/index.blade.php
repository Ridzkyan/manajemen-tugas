@extends('layouts.admin')

@section('title', 'Monitoring')

@section('content')
<div class="container">
    <h4>Monitoring Aktivitas</h4>

    <div class="my-4 p-3 bg-white rounded shadow">
        <h5>Status Online</h5>
        <div class="my-4 p-3 bg-white rounded shadow">
            <h5>Detail Pengguna</h5>

            @foreach($allUsers as $role => $users)
                <h6 class="mt-3 text-capitalize">{{ $role }}</h6>
                <ul class="list-group">
                    @foreach($users as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $user->name }}</strong> <br>
                                <small>{{ $user->email }}</small>
                            </div>
                            @if($user->last_login_at && $user->last_login_at >= $threshold)
                                <span class="badge bg-success">Online</span>
                            @else
                                <span class="badge bg-danger">Offline</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>

    <div class="my-4 p-3 bg-white rounded shadow">
        <h5>Grafik Materi per Kelas</h5>
        <canvas id="grafikKelas"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikKelas').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($kelasTeraktif->pluck('nama_kelas')) !!},
            datasets: [{
                label: 'Jumlah Materi',
                data: {!! json_encode($kelasTeraktif->pluck('materi_count')) !!},
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
