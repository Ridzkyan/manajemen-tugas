@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container py-4">
    <h4 class="fw-bold mb-4 text-dark">
        <i class="fas fa-users text-warning me-2"></i> Manajemen Pengguna
    </h4>

    @if(session('success'))
    <div class="custom-alert" id="success-alert">
        <span><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
    @endif

    <a href="{{ route('admin.users.create') }}" class="btn mb-3 text-white" style="background-color: #008080;">+ Tambah User</a>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex flex-row gap-2">
            <div class="input-group input-group-sm shadow-sm rounded">
                <span class="input-group-text bg-white">
                    <i class="fas fa-user-tag text-secondary"></i>
                </span>
                <select id="roleFilter" class="form-select border-start-0">
                    <option value="all">Semua</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </select>
            </div>
            <div class="input-group input-group-sm shadow-sm rounded">
                <span class="input-group-text bg-white">
                    <i class="fas fa-sort-alpha-down text-secondary"></i>
                </span>
                <select id="sortAZ" class="form-select border-start-0">
                    <option value="asc">Urutkan A-Z</option>
                    <option value="desc">Urutkan Z-A</option>
                </select>
            </div>
        </div>
        <div class="input-group input-group-sm shadow-sm rounded" style="max-width: 250px;">
            <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari Nama Pengguna...">
        </div>
    </div>

    @php
        $roleLabels = ['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'];
        $icons = ['admin' => 'fa-user-shield', 'dosen' => 'fa-chalkboard-teacher', 'mahasiswa' => 'fa-user-graduate'];
        $groupedUsers = $users->groupBy('role');
    @endphp

    @foreach($groupedUsers as $role => $group)
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-header bg-white fw-semibold fs-6 align-grid toggle-header" data-role="{{ $role }}">
            <span class="section-label">
                <i class="fas {{ $icons[$role] ?? 'fa-users' }} text-warning me-2"></i>{{ $roleLabels[$role] ?? ucfirst($role) }}
            </span>
            <i class="fas fa-chevron-down rotate-icon"></i>
        </div>
        <div class="card-body user-section d-none" id="section-{{ $role }}">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Kode Unik</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $user)
                        <tr class="user-row group-{{ $role }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge" style="background-color: #f5a04e;">{{ ucfirst($user->role) }}</span></td>
                            <td>{{ $user->role === 'dosen' ? $user->kode_unik : '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete('{{ route('admin.users.destroy', $user->id) }}')">Hapus</button>
                                    <button class="btn btn-sm btn-dark" onclick="confirmReset('{{ route('admin.users.reset-password', $user->id) }}')">Reset PW</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    function filterTable() {
        const selectedRole = document.getElementById('roleFilter').value;
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const sortDirection = document.getElementById('sortAZ').value;

        document.querySelectorAll('.user-section').forEach(section => {
            section.classList.remove('active');
        });

        document.querySelectorAll('.user-row').forEach(row => {
            const name = row.cells[0].textContent.toLowerCase();
            const role = row.querySelector('span.badge')?.innerText.toLowerCase();
            const matchRole = selectedRole === 'all' || role === selectedRole;
            const matchSearch = name.includes(searchText);
            const isVisible = matchRole && matchSearch;
            row.style.display = isVisible ? '' : 'none';

            if (isVisible) {
                const parentSection = row.closest('.user-section');
                parentSection?.classList.add('active');
                parentSection?.previousElementSibling.querySelector('.rotate-icon')?.classList.add('rotate');
            }
        });

        document.querySelectorAll('.user-section tbody').forEach(tbody => {
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
            rows.sort((a, b) => {
                const nameA = a.cells[0].innerText.toLowerCase();
                const nameB = b.cells[0].innerText.toLowerCase();
                return sortDirection === 'asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
            });
            rows.forEach(row => tbody.appendChild(row));
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-header').forEach(header => {
            header.addEventListener('click', function () {
                const role = this.dataset.role;
                const section = document.getElementById(`section-${role}`);
                const icon = this.querySelector('.rotate-icon');
                section.classList.toggle('active'); // tampilkan/sematikan
                section.classList.toggle('d-none'); // hilangkan d-none saat tampil
                icon.classList.toggle('rotate');
            });
        });

        document.getElementById('roleFilter').addEventListener('change', filterTable);
        document.getElementById('searchInput').addEventListener('input', filterTable);
        document.getElementById('sortAZ').addEventListener('change', filterTable);
    });
</script>

<script>
function confirmDelete(url) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data tidak bisa dikembalikan setelah dihapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmReset(url) {
    Swal.fire({
        title: 'Reset Password?',
        text: "Password akan direset ke default (misal: 12345678).",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#343a40',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, reset!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection