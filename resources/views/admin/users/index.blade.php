@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .custom-alert {
        padding: 15px 20px;
        border-left: 6px solid #28a745;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #d4edda;
        color: #155724;
        animation: fadeSlideDown 0.4s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        position: relative;
    }

    @keyframes fadeSlideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .custom-alert .btn-close {
        position: absolute;
        top: 10px;
        right: 15px;
    }

    .role-toggle-btn {
        background-color: #008080;
        color: #fff;
        font-weight: 50;
        padding: 2px 6px;
        border: none;
        border-radius: 6px;
        transition: background-color 0.3s ease;
    }

    .role-toggle-btn:hover {
        background-color: #f5a04e;
    }

    .user-group.hidden {
        display: none;
    }

    .btn-group-aksi {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    @media (max-width: 768px) {
        .table td,
        .table th {
            font-size: 14px;
            padding: 8px 6px;
            word-break: break-word;
        }

        .btn-group-aksi {
            flex-direction: column;
            gap: 6px;
        }
    }

    select.form-select,
    input.form-control {
        transition: box-shadow 0.2s ease;
    }

    input.form-control:focus,
    select.form-select:focus {
        box-shadow: 0 0 5px rgba(0, 131, 143, 0.5);
        border-color: #00838f;
    }
</style>

<div class="bg-white p-4 rounded shadow" style="border-left: 6px solid #008080;">
    <h4 class="fw-bold mb-4 text-dark"><i class="fas fa-users me-2"></i> Manajemen Pengguna</h4>

    <a href="{{ route('admin.create') }}" class="btn mb-3 text-white" style="background-color: #008080;">+ Tambah User</a>

    @if(session('success'))
        <div class="custom-alert" id="success-alert">
            <span><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <!-- Filter Role & Sort -->
        <div class="d-flex flex-row gap-2">
            <!-- Role Filter -->
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
    
            <!-- Sort Option -->
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
    
        <!-- Search Box -->
        <div class="input-group input-group-sm shadow-sm rounded" style="max-width: 250px;">
            <span class="input-group-text bg-white">
                <i class="fas fa-search text-secondary"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari Nama Pengguna...">
        </div>
    </div>     

    <div class="table-responsive rounded shadow-sm">
        <table class="table table-bordered text-center mb-0">
            <thead style="background-color: #008080; color: white;">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Kode Unik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                @php
                    $roleLabels = ['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'];
                    $groupedUsers = $users->groupBy('role');
                @endphp

                @foreach($groupedUsers as $role => $group)
                    <tr class="bg-light">
                        <td colspan="5" class="text-start fw-bold">
                            <button class="role-toggle-btn toggle-role" data-role="{{ $role }}">
                                <i class="fas fa-chevron-right me-1"></i> {{ $roleLabels[$role] ?? ucfirst($role) }}
                            </button>
                        </td>
                    </tr>
                    <tbody id="group-{{ $role }}" class="user-group hidden">
                        @foreach($group as $user)
                        <tr class="user-row group-{{ $role }}" style="background-color: #fef9f4;">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge" style="background-color: #f5a04e;">{{ ucfirst($user->role) }}</span></td>
                            <td>{{ $user->role === 'dosen' ? $user->kode_unik : '-' }}</td>
                            <td>
                                <div class="btn-group-aksi">
                                    <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-sm text-white" style="background-color: #ffc107;">Edit</a>
                                    <button type="button" class="btn btn-sm text-white" style="background-color: #dc3545;" onclick="confirmDelete('{{ route('admin.destroy', $user->id) }}')">Hapus</button>
                                    <button type="button" class="btn btn-sm text-white" style="background-color: #343a40;" onclick="confirmReset('{{ route('admin.reset.password', $user->id) }}')">Reset PW</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle Role Table
        document.querySelectorAll('.toggle-role').forEach(btn => {
            const role = btn.dataset.role;
            const group = document.getElementById(`group-${role}`);
            const icon = btn.querySelector('i');
            btn.addEventListener('click', () => {
                const isHidden = group.classList.contains('hidden');
                group.classList.toggle('hidden', !isHidden);
                icon.classList.toggle('fa-chevron-down', isHidden);
                icon.classList.toggle('fa-chevron-right', !isHidden);
            });
        });

        const roleFilter = document.getElementById('roleFilter');
        const sortAZ = document.getElementById('sortAZ');
        const searchInput = document.getElementById('searchInput');

        function filterTable() {
            const selectedRole = roleFilter.value;
            const searchText = searchInput.value.toLowerCase();
            const sortDirection = sortAZ.value;

            // Sembunyikan semua group (tbody)
            document.querySelectorAll('.user-group').forEach(group => {
                group.classList.add('hidden');
                group.classList.remove('visible');
            });

            // Tampilkan group sesuai role
            if (selectedRole === 'all') {
                document.querySelectorAll('.user-group').forEach(group => {
                    group.classList.remove('hidden');
                    group.classList.add('visible');
                });
            } else {
                const selectedGroup = document.getElementById(`group-${selectedRole}`);
                if (selectedGroup) {
                    selectedGroup.classList.remove('hidden');
                    selectedGroup.classList.add('visible');
                }
            }

            // Tampilkan atau sembunyikan baris user berdasarkan filter
            const rows = document.querySelectorAll('.user-row');
            const filteredRows = Array.from(rows).filter(row => {
                const role = row.querySelector('span.badge')?.innerText.toLowerCase();
                const name = row.cells[0].innerText.toLowerCase();
                const matchRole = selectedRole === 'all' || role === selectedRole;
                const matchSearch = name.includes(searchText);
                row.style.display = (matchRole && matchSearch) ? '' : 'none';
                return row.style.display !== 'none';
            });

            // Urutkan
            filteredRows.sort((a, b) => {
                const nameA = a.cells[0].innerText.toLowerCase();
                const nameB = b.cells[0].innerText.toLowerCase();
                return sortDirection === 'asc' ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
            });

            // Masukkan kembali ke DOM
            const allGroups = document.querySelectorAll('.user-group');
            allGroups.forEach(group => {
                filteredRows
                    .filter(row => group.contains(row))
                    .forEach(row => group.appendChild(row));
            });
        }

        roleFilter.addEventListener('change', filterTable);
        sortAZ.addEventListener('change', filterTable);
        searchInput.addEventListener('input', filterTable);

        const alert = document.getElementById('success-alert');
        if (alert) {
            setTimeout(() => {
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 300);
            }, 1000);
        }
    });

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