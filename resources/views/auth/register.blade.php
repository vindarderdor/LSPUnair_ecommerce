<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Baru - Tokomedia</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .register-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }
        .card-header-custom {
            background: #f8fafc;
            padding: 1.75rem 2rem 1.25rem;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="card-header-custom">
        <a href="{{ route('shop.index') }}" class="d-inline-block mb-3">
            <img src="{{ asset('images/logo-dark.png') }}" alt="Tokomedia" height="42">
        </a>
        <h3 class="fw-bold mb-1 text-dark">Daftar Akun Baru</h3>
        <p class="text-muted small mb-0">Lengkapi data diri untuk mulai berbelanja di Tokomedia</p>
    </div>

    <div class="p-4 p-md-5">
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger text-sm py-2 px-3 mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nama_user" class="form-label fw-semibold text-secondary small">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="nama_user" name="nama_user" value="{{ old('nama_user') }}" placeholder="Nama Lengkap Anda" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-secondary small">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control border-start-0" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label fw-semibold text-secondary small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" class="form-control border-start-0" id="password" name="password" placeholder="Min. 6 digit" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold text-secondary small">Konfirmasi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted"></i></span>
                        <input type="password" class="form-control border-start-0" id="password_confirmation" name="password_confirmation" placeholder="Ulangi" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm mb-3">
                <i class="fas fa-user-plus me-1"></i> Buat Akun Sekarang
            </button>
        </form>

        <div class="text-center text-muted small mt-3">
            Sudah memiliki akun? <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">Masuk di sini</a>
            <div class="mt-2">
                <a href="{{ route('shop.index') }}" class="text-secondary text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Toko
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
