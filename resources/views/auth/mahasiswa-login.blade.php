<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Mahasiswa - TaskFlow</title>

  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

  <link href="{{ asset('css/auth/variable.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/auth/mahasiswa/login.css') }}">

</head>
<body>

  <div class="welcome-card">

    {{-- Title & Subtitle --}}
    <div class="welcome-title">Login Mahasiswa</div>
    <div class="welcome-subtitle">Masuk untuk mengakses kelas dan tugas</div>

    {{-- Alerts --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('login.mahasiswa') }}">
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

      <button type="submit" class="btn btn-custom w-100">
        <i class="fas fa-sign-in-alt me-2"></i> Masuk
      </button>
    </form>

    {{-- Sign Up Text --}}
    <div class="signup-text">
      Belum punya akun? <a href="{{ route('register.mahasiswa') }}">Sign Up</a>
    </div>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
