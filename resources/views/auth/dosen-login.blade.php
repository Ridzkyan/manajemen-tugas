<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Dosen - TaskFlow</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <style>
        html, body {
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Nunito', sans-serif;
            overflow: hidden;
        }

        body {
            background: url('{{ asset('images/Background.png') }}') center/cover no-repeat fixed;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .welcome-card {
            background: transparent;
            color: white;
            text-align: center;
            width: 100%;
            max-width: 400px;
            padding: 3rem 2rem;
            border-radius: 20px;
        }

        .logo-img {
            width: 80px;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 2px rgba(0,0,0,0.6));
        }

        .welcome-title {
            font-size: 1.7rem;
            font-weight: bold;
            margin-bottom: 5px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }

        .welcome-subtitle {
            font-size: 0.95rem;
            margin-bottom: 25px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.6);
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.25);
            border: none;
            color: white;
            font-weight: 600;
            text-shadow: 0 0 1px rgba(0,0,0,0.7);
        }
        .form-control::placeholder {
            color: #e0e0e0;
            font-weight: 400;
            text-shadow: none;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 0.15rem rgba(255, 179, 71, 0.4);
            border-color: #ffb347;
            color: #000;
            outline: none;
            text-shadow: none;
        }

        .btn-custom {
            background-color: #FFB347;
            color: #000;
            font-weight: bold;
            border-radius: 30px;
            padding: 10px;
            font-size: 1rem;
            height: 48px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 8px rgba(255,179,71,0.6);
        }

        .btn-custom:hover {
            background-color: #ffffff;
            color: #008080;
            box-shadow: 0 6px 12px rgba(0,128,128,0.8);
        }

        .alert {
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        @media (max-width: 768px) {
            html, body {
                height: auto;
                overflow-y: auto;
            }

            body {
                align-items: flex-start;
                padding: 40px 10px;
            }

            .welcome-card {
                padding: 2rem 1.5rem;
                max-width: 100%;
                border-radius: 10px;
                background: transparent;
                box-shadow: none;
            }

            .logo-img {
                width: 60px;
                margin-bottom: 15px;
                filter: none;
            }

            .welcome-title {
                font-size: 1.4rem;
            }

            .btn-custom {
                height: 45px;
                font-size: 0.95rem;
            }

            .form-control {
                background-color: rgba(255, 255, 255, 0.25);
                color: white;
                font-weight: 600;
                text-shadow: 0 0 1px rgba(0,0,0,0.7);
            }

            .form-control::placeholder {
                color: #e0e0e0;
                text-shadow: none;
            }

            .form-control:focus {
                background-color: rgba(255, 255, 255, 0.4);
                color: #000;
                text-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="welcome-card">

       
        {{-- Title & Subtitle --}}
        <div class="welcome-title">Login Dosen</div>
        <div class="welcome-subtitle">Masuk untuk mengelola kelas & materi</div>

        {{-- ALERT --}}
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

        {{-- FORM --}}
        <form method="POST" action="{{ route('dosen.login') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    required
                    placeholder="Masukkan email"
                    value="{{ old('email') }}"
                />
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                    placeholder="Masukkan password"
                />
            </div>

            <div class="mb-4 text-start">
                <label for="kode_unik" class="form-label">Kode Unik</label>
                <input
                    type="text"
                    id="kode_unik"
                    name="kode_unik"
                    class="form-control"
                    required
                    placeholder="Masukkan kode kelas"
                    value="{{ old('kode_unik') }}"
                />
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
