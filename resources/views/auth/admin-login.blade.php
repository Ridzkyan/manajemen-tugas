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
    <style>
        body {
            background: url('{{ asset("images/Background.png") }}') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Nunito', sans-serif;
        }

        /* Style untuk card */
        .login-card {
            background-color: #008080;
            color: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
        }

        .logo-img {
            width: 100px;
            margin-bottom: 20px;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .btn-custom {
            background-color: #FFB347;
            color: #000;
            font-weight: bold;
            border-radius: 30px;
            padding: 10px 20px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background: #ffffff;
            color: #008080;
        }

        .form-group {
            margin-bottom: 1rem;
        }
    </style>
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
</body>
</html>