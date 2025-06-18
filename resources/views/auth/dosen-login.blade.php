<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Dosen - TaskFlow</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
     <link href="{{ asset('css/auth/variable.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth/dosen/login.css') }}" rel="stylesheet" />
</head>
<body>
    <div class="welcome-card">
        <div class="welcome-title">Login Dosen</div>
        <div class="welcome-subtitle">Masuk untuk mengelola kelas & materi</div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('dosen.login') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="Masukkan email" value="{{ old('email') }}" />
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan password" />
            </div>

            <div class="mb-4 text-start">
                <label for="kode_unik" class="form-label">Kode Unik</label>
                <input type="text" id="kode_unik" name="kode_unik" class="form-control" required placeholder="Masukkan kode kelas" value="{{ old('kode_unik') }}" />
            </div>

            <button type="submit" class="btn btn-custom w-100">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk
            </button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
