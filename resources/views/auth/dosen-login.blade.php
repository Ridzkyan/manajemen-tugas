<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Dosen - TaskFlow</title>
  
  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #fff9f4;
        overflow: hidden;
        height: 100vh;
    }
    .login-container {
      display: flex;
      min-height: 100vh;
    }
    .text-taskflow {
      color: #008080;
      font-weight: bold;
    }
    .left-pane {
      flex: 1;
      background: url('{{ asset('images/Logo.png') }}') center/cover no-repeat;
      padding: 2rem;
      color: white;
      font-weight: bold;
      position: relative;
    }
    .right-pane {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 6rem 2rem 4rem;
      background: linear-gradient(to bottom right, #fff9f4, #fefefe);
    }
    .login-form {
      width: 100%;
      max-width: 460px;
      margin-top: 20px;
    }
    .login-form h3 {
      font-weight: bold;
    }
    .form-control:focus {
      box-shadow: 0 0 0 0.1rem rgba(0,128,128,.25);
      border-color: #008080;
    }
    .btn-login {
      background-color: #008080;
      color: white;
      border-radius: 30px;
      padding: 10px 20px;
      font-weight: bold;
      border: none;
    }
    .btn-login:hover {
      background-color: #f5a04e;
    }
    .social-icons i {
      font-size: 20px;
      margin: 0 10px;
      cursor: pointer;
      color: #555;
    }
    .top-right-button {
      position: absolute;
      top: 20px;
      right: 40px;
    }
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }

      .left-pane {
        display: none;
      }

      .right-pane {
        padding: 2rem;
        align-items: center;
        text-align: center;
      }

      .login-form {
        max-width: 100%;
      }

      .login-form input {
        font-size: 16px;
      }

      .login-form button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="left-pane">
      <div class="mt-2">TASKFLOW - MANAGEMENT</div>
    </div>

    <div class="right-pane">
      <div class="login-form">

        {{-- ALERT (Form Validation, Error Login) --}}
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show small py-2 px-3 mb-3" role="alert" style="font-size: 0.9rem;">
            {{ session('error') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('message'))
          <div class="alert alert-success alert-dismissible fade show small py-2 px-3 mb-3" role="alert" style="font-size: 0.9rem;">
            {{ session('message') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show small py-2 px-3 mb-3" role="alert" style="font-size: 0.9rem;">
            {{ $errors->first() }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        {{-- FORM LOGIN --}}
        <form method="POST" action="{{ route('dosen.login') }}">
          @csrf
          <h3 class="fw-bold mb-2">Selamat Datang di <span class="text-taskflow">TaskFlow</span>!</h3>
          <p class="text-muted mb-4">Log In akun</p>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan username/email" value="{{ old('email') }}">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan Password">
          </div>

          <div class="mb-4">
            <label for="kode_unik" class="form-label">Kode Unik</label>
            <input type="text" class="form-control" id="kode_unik" name="kode_unik" required placeholder="Masukkan Kode Unik" value="{{ old('kode_unik') }}">
          </div>

          <button type="submit" class="btn btn-login w-100">
              <i class="fas fa-sign-in-alt me-1"></i> Log In
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  {{-- SweetAlert2 Logout Success --}}
  @if (session('success'))
  <script>
      Swal.fire({
          icon: 'success',
          title: 'Logout Berhasil',
          text: '{{ session('success') }}',
          showConfirmButton: false,
          timer: 2000
      });
  </script>
  @endif
</body>
</html>