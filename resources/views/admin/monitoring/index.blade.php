@extends('layouts.admin')

@section('title', 'Monitoring')

@section('content')
<link rel="stylesheet" href="{{ asset('css/backsite/admin/monitoring.css') }}">

<div class="container">
    <h4 class="fw-bold mb-4 text-teal">Monitoring Aktivitas</h4>

    {{-- FILTER & SEARCH --}}
    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
        <div>
            <select id="roleFilter" class="form-select">
                <option value="all">Semua</option>
                <option value="admin">Admin</option>
                <option value="dosen">Dosen</option>
                <option value="mahasiswa">Mahasiswa</option>
            </select>
        </div>
        <div>
            <input type="text" id="searchInput" class="form-control" placeholder="Ketik nama atau email...">
        </div>
    </div>

    {{-- STATUS ONLINE --}}
    <div class="mb-5 bg-white p-4 rounded-4 shadow-sm">
        <h5 class="fw-bold mb-4 text-orange">Status Online</h5>

        <div id="userList" class="d-grid gap-4">
            @foreach($allUsers as $role => $users)
                @php $lowerRole = strtolower($role); @endphp
                <div class="user-group" data-role="{{ $lowerRole }}">
                    <h6 class="text-teal fw-semibold mb-3">{{ ucfirst($lowerRole) }}</h6>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                        @foreach($users as $user)
                            <div class="col user-item">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 user-name">{{ $user->name }}</h6>
                                            <small class="text-muted user-email">{{ $user->email }}</small>
                                        </div>
                                        <span class="badge px-3 py-2 rounded-pill"
                                              style="background-color: {{ $user->is_online ? '#008080' : '#dc3545' }}; color: white;">
                                            {{ $user->is_online ? 'Online' : 'Offline' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- GRAFIK MATERI --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <h5 class="fw-bold text-orange mb-4">Grafik Materi Terbanyak per Kelas</h5>
            <div class="bg-light rounded-4 p-4">
                <canvas id="grafikKelas" height="300"></canvas>
            </div>
        </div>
    </div>

    {{-- GRAFIK TUGAS --}}
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body">
            <h5 class="fw-bold text-orange mb-4">Grafik Tugas Terbanyak per Kelas</h5>
            <div class="bg-light rounded-4 p-4">
                <canvas id="grafikTugas" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const roleFilter = document.getElementById('roleFilter');
    const searchInput = document.getElementById('searchInput');

    function filterUsers() {
        const selectedRole = roleFilter.value.toLowerCase();
        const searchText = searchInput.value.toLowerCase();

        document.querySelectorAll('.user-group').forEach(group => {
            const groupRole = group.getAttribute('data-role').toLowerCase();
            const userItems = group.querySelectorAll('.user-item');
            let groupHasVisible = false;

            userItems.forEach(item => {
                const name = item.querySelector('.user-name')?.textContent.toLowerCase() || '';
                const email = item.querySelector('.user-email')?.textContent.toLowerCase() || '';
                const matchRole = selectedRole === 'all' || selectedRole === groupRole;
                const matchSearch = searchText === '' || name.includes(searchText) || email.includes(searchText);
                const isVisible = matchRole && matchSearch;

                item.style.setProperty('display', isVisible ? 'block' : 'none', 'important');
                if (isVisible) groupHasVisible = true;
            });

            group.style.display = groupHasVisible ? 'block' : 'none';
        });
    }

    roleFilter.addEventListener('change', filterUsers);
    searchInput.addEventListener('input', filterUsers);
    document.addEventListener('DOMContentLoaded', filterUsers);

    // CHART MATERI
    new Chart(document.getElementById('grafikKelas').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($kelasTeraktif->pluck('nama_kelas')) !!},
            datasets: [{
                label: 'Jumlah Materi',
                data: {!! json_encode($kelasTeraktif->pluck('materi_count')) !!},
                backgroundColor: '#008080',
                borderRadius: 6,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, ticks: { color: '#333' } },
                y: { ticks: { color: '#333' } }
            },
            plugins: {
                legend: { labels: { color: '#333', font: { weight: 'bold' } } },
                tooltip: {
                    backgroundColor: '#f5a04e',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            }
        }
    });

    // CHART TUGAS
    new Chart(document.getElementById('grafikTugas').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($kelasTugasTerbanyak->pluck('nama_kelas')) !!},
            datasets: [{
                label: 'Jumlah Tugas',
                data: {!! json_encode($kelasTugasTerbanyak->pluck('tugas_count')) !!},
                backgroundColor: '#f5a04e',
                borderRadius: 6,
                barThickness: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, ticks: { color: '#333' } },
                y: { ticks: { color: '#333' } }
            },
            plugins: {
                legend: { labels: { color: '#333', font: { weight: 'bold' } } },
                tooltip: {
                    backgroundColor: '#008080',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            }
        }
    });
</script>
@endsection
