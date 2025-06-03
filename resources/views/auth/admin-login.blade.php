<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - TaskFlow</title>

    <!-- Bootstrap + FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="{{ asset('css/frontsite/admin/admin_login.css') }}" rel="stylesheet">
</head>

<body>
    <div class="login-card">
        <img src="{{ asset('images/LogoWelcome.png') }}" alt="TaskFlow Logo" class="logo-img">
        <div class="welcome-title">Login Admin TaskFlow</div>
        <p class="text-muted mb-4">Masukkan email dan password untuk login ke dashboard admin.</p>
        
        <form action="{{ route('admin.login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Masukkan email Anda" value="{{ old('email') }}" required>
                @error('email')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukkan password Anda" required>
                @error('password')
                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-custom w-100 mt-4">Log In</button>
        </form>
    </div>

    <!-- Bootstrap + FontAwesome JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('logout_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Logout',
            text: '{{ session('logout_success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    @endif

    @if(session('login_failed'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '{{ session('login_failed') }}',
            showConfirmButton: true
        });
    </script>
    @endif

    @if(session('login_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Login Berhasil',
            text: '{{ session('login_success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    @endif
</body>
</html>
