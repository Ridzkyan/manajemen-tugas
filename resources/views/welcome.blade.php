<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow Management - Welcome</title>

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
        .welcome-card {
            background: #008080;
            color: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
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
    </style>
</head>

<body>
    <div class="welcome-card">
        <img src="{{ asset('images/LogoWelcome.png') }}" alt="TaskFlow Logo" class="logo-img">
        <div class="welcome-title">TaskFlow<br>Management</div>
        <div class="welcome-subtitle">Kelola tugasmu dengan mudah di TaskFlow.</div>

        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('login') }}" class="btn btn-custom">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn btn-custom">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </div>
    </div>

    <!-- Bootstrap + FontAwesome JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
