<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow Management - Welcome</title>

    <!-- Bootstrap + FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- External CSS -->
    <link href="{{ asset('css/frontsite/welcome.css') }}" rel="stylesheet">
</head>

<body>
    <div class="welcome-card">
        <img src="{{ asset('images/LogoWelcome.png') }}" alt="TaskFlow Logo" class="logo-img">
        <div class="welcome-title">TaskFlow<br>Management</div>
        <div class="welcome-subtitle">Kelola tugasmu dengan mudah di TaskFlow.</div>

        <div class="d-grid gap-3">
            <a href="{{ url('/dosen/login') }}" class="btn btn-custom d-flex align-items-center justify-content-center w-100">
                <i class="fas fa-chalkboard-teacher me-2"></i> Login Dosen
            </a>
            <a href="{{ url('/mahasiswa/login') }}" class="btn btn-custom d-flex align-items-center justify-content-center w-100">
                <i class="fas fa-user-graduate me-2"></i> Login Mahasiswa
            </a>
        </div>
    </div>

    <!-- Bootstrap + FontAwesome JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
