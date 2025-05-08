@extends('layouts.admin')

@section('content')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    html, body {
        height: 100vh;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .setting-wrapper {
        padding: 1rem 2rem 2rem;
        min-height: 85vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        animation: fadeSlideUp 0.6s ease;
    }

    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .setting-title {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        font-weight: bold;
        margin-bottom: 0.3rem; /* dikurangi dari sebelumnya */
        margin-top: 0.8rem;     /* opsional, bisa diatur 0.6-0.8 */
    }

    .setting-title i {
        color: #f5a04e;
        margin-right: 0.6rem;
    }

    .setting-subtext {
        text-align: center;
        color: #666;
        margin-bottom: 1.5rem;  /* dikurangi supaya card lebih dekat */
    }

    .setting-grid-row {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .setting-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
        padding: 3.5rem 2.5rem;
        text-align: center;
        position: relative;
        transition: transform 0.2s ease;
        width: 280px;
        height: 280px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .setting-card:hover {
        transform: translateY(-6px);
    }

    .setting-card i {
        font-size: 3rem;
        margin-bottom: 1rem;
        transition: transform 0.2s ease;
    }

    .setting-card:hover i {
        transform: scale(1.1);
    }

    .setting-card .badge {
        position: absolute;
        top: 12px;
        right: 16px;
        font-size: 0.7rem;
        padding: 0.3em 0.6em;
    }

    @media (max-width: 768px) {
        .setting-grid-row {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<div class="setting-wrapper">
    <div class="setting-title">
        <i class="fas fa-cogs"></i> Pengaturan Admin
    </div>
    <p class="setting-subtext">Kelola pengaturan akun admin dan sistem di bawah ini.</p>

    <div class="setting-grid-row">
        <a href="{{ route('admin.profil.edit') }}" class="setting-card text-decoration-none text-dark">
            <i class="fas fa-user-cog text-primary"></i>
            <div class="badge bg-primary text-white">Aktif</div>
            <div class="fw-bold mt-2">Ubah Profile</div>
        </a>
        <a href="{{ route('admin.password.edit') }}" class="setting-card text-decoration-none text-dark">
            <i class="fas fa-key text-danger"></i>
            <div class="badge bg-danger text-white">Keamanan</div>
            <div class="fw-bold mt-2">Ganti Password</div>
        </a>
    </div>

    <div class="setting-grid-row">
        <div class="setting-card">
            <i class="fas fa-bell text-info"></i>
            <div class="badge bg-secondary text-white">Soon</div>
            <div class="fw-bold mt-2">Notifikasi</div>
        </div>
        <div class="setting-card">
            <i class="fas fa-sliders-h text-secondary"></i>
            <div class="badge bg-secondary text-white">Soon</div>
            <div class="fw-bold mt-2">Sistem</div>
        </div>
        <div class="setting-card text-danger" onclick="handleLogout()" style="cursor: pointer;">
            <i class="fas fa-sign-out-alt"></i>
            <div class="badge bg-danger text-white">Logout</div>
            <div class="fw-bold mt-2">Logout</div>
            <form id="logoutForm" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>

<script>
    function handleLogout() {
        Swal.fire({
            title: 'Yakin ingin melakukan logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            animation: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }
</script>

@endsection