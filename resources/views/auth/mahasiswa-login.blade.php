<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Mahasiswa - TaskFlow</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #fff9f4;
      overflow: hidden;
    }
    .login-container {
      display: flex;
      min-height: 100vh;
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
      padding: 2rem 4rem;
      background: linear-gradient(to bottom right, #fff9f4, #fefefe);
      position: relative;
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
    .btn-signin {
  background-color: #008080;
        color: white;
        font-weight: bold;
        padding: 6px 16px;
        border-radius: 20px;
        border: none;
        transition: background-color 0.3s ease;
    }

    .btn-signin:hover {
        background-color: #f5a04e;
        color: white;
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
    <!-- Left Pane -->
    <div class="left-pane">
      <div class="mt-2">TASKFLOW - MANAGEMENT</div>
    </div>

    <!-- Right Pane -->
    <div class="right-pane">
      <!-- Sign In Link -->
      <div class="position-absolute top-0 end-0 p-4 d-flex align-items-center gap-2">
        <span class="fw-semibold text-muted">Belum punya akun</span>
        <a href="{{ route('register.mahasiswa') }}" class="btn btn-signin">Sign Up</a>
      </div>
      <!-- Form -->
      <form class="login-form" method="POST" action="{{ route('mahasiswa.login') }}">
        @csrf
        <h3 class="fw-bold mb-2">Selamat Datang di <span style="color: #008080;">TaskFlow</span>!</h3>
        <p class="text-muted mb-4">Log In akun</p>
        <div class="mb-3">
          <label for="email" class="form-label">Username/Email</label>
          <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan username/email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan Password">
        </div>
        <button type="submit" class="btn btn-login w-100">
          <i class="fas fa-sign-in-alt me-1"></i> Log In
        </button>
      </form>
    </div>
  </div>
</body>
</html>