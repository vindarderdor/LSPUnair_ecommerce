<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Toko Online E-Commerce Sederhana" />
    <title>@yield('title', 'Shop Homepage - Ecommerce')</title>

    <!-- Favicon-->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />

    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />

    @stack('styles')
    <style>
        .navbar-custom {
            background-color: #ffffff !important;
            border-bottom: 1px solid #eef2f6;
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
        }
        .navbar-custom .navbar-brand {
            padding-top: 0;
            padding-bottom: 0;
        }
        .navbar-custom .nav-link {
            color: #4b5563 !important;
            font-size: 0.925rem;
            font-weight: 500;
            padding: 0.45rem 0.85rem !important;
            border-radius: 0.5rem;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .navbar-custom .nav-link:hover {
            color: #111827 !important;
            background-color: #f3f4f6;
        }
        .navbar-custom .nav-link.active {
            color: #111827 !important;
            font-weight: 600;
        }
        .nav-shortcut-btn {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            color: #374151;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.38rem 0.85rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .nav-shortcut-btn:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
        }
        .user-profile-btn {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            color: #1f2937;
            border-radius: 9999px;
            padding: 0.3rem 0.75rem 0.3rem 0.4rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .user-profile-btn:hover, .user-profile-btn:focus, .show > .user-profile-btn {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
        }
        .user-profile-btn::after {
            color: #9ca3af;
            margin-left: 0.25rem;
            vertical-align: middle;
        }
        .user-avatar-circle {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background-color: #f3f4f6;
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        .role-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            text-transform: capitalize;
        }
        .role-badge-penjual {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }
        .role-badge-pembeli {
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }
        .btn-nav-guest-login {
            color: #374151;
            background-color: transparent;
            border: 1px solid #d1d5db;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.38rem 0.95rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-nav-guest-login:hover {
            background-color: #f9fafb;
            color: #111827;
            border-color: #9ca3af;
        }
        .btn-nav-guest-register {
            color: #ffffff;
            background-color: #111827;
            border: 1px solid #111827;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 0.38rem 1rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-nav-guest-register:hover {
            background-color: #1f2937;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand fw-bold d-flex align-items-center me-4" href="{{ route('shop.index') }}">
                <img src="{{ asset('images/logo-dark.png') }}" alt="Tokomedia" height="30" class="d-inline-block">
            </a>
            <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.index') && !request('kategori') && !request('q') ? 'active' : '' }}" href="{{ route('shop.index') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">Keranjang</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request('kategori') ? 'active' : '' }}" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Kategori
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm rounded-3 mt-1" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item py-2" href="{{ route('shop.index') }}"><i class="bi-grid me-2 text-muted"></i>Semua Produk</a></li>
                            <li><hr class="dropdown-divider my-1" /></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.index', ['kategori' => 'Fashion Pria']) }}">Fashion Pria</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.index', ['kategori' => 'Aksesoris']) }}">Aksesoris</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.index', ['kategori' => 'Elektronik']) }}">Elektronik</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('shop.index', ['kategori' => 'Perlengkapan Rumah']) }}">Perlengkapan Rumah</a></li>
                        </ul>
                    </li>
                </ul>

                {{-- Search Bar ala Tokopedia (Monochrome Theme) --}}
                <form action="{{ route('shop.index') }}" method="GET" class="flex-grow-1 mx-lg-3 my-2 my-lg-0" style="max-width: 480px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-3 pe-2" style="border-color: #e2e8f0;">
                            <i class="bi-search small"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               value="{{ request('q') }}" 
                               class="form-control border-start-0 border-end-0 shadow-none ps-1" 
                               style="border-color: #e2e8f0; font-size: 0.875rem;" 
                               placeholder="Cari produk di Tokomedia..." 
                               aria-label="Cari produk">
                        <button class="btn btn-dark rounded-end-pill px-3" type="submit" style="font-size: 0.85rem;">
                            Cari
                        </button>
                    </div>
                </form>

                <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                    {{-- Tombol Keranjang Belanja ala Tokopedia (Monokrom) --}}
                    @php
                        $cartItemsCount = is_array(session('cart')) ? array_sum(array_column(session('cart'), 'qty')) : 0;
                    @endphp
                    <a href="{{ route('cart.index') }}" class="nav-shortcut-btn position-relative me-1 {{ request()->routeIs('cart.index') ? 'bg-light border-secondary text-dark' : '' }}" title="Keranjang Belanja">
                        <i class="bi-cart3 text-dark"></i>
                        <span class="d-none d-sm-inline">Keranjang</span>
                        @if($cartItemsCount > 0)
                            <span class="badge rounded-pill bg-dark text-white border border-white ms-1" style="font-size: 0.7rem;">
                                {{ $cartItemsCount > 99 ? '99+' : $cartItemsCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        @if(Auth::user()->isPembeli())
                            <a href="{{ route('shop.orders') }}" class="nav-shortcut-btn {{ request()->routeIs('shop.orders') ? 'bg-light border-secondary' : '' }}" title="Pesanan Saya">
                                <i class="bi-bag-check text-muted"></i>
                                <span>Pesanan Saya</span>
                            </a>
                        @elseif(Auth::user()->isPenjual())
                            <a href="{{ route('admin.dashboard') }}" class="nav-shortcut-btn" title="Dashboard Admin">
                                <i class="bi-speedometer2 text-muted"></i>
                                <span>Dashboard Admin</span>
                            </a>
                        @endif

                        <div class="dropdown">
                            <button class="user-profile-btn dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="user-avatar-circle">
                                    <i class="bi-person-fill"></i>
                                </span>
                                <span class="fw-semibold text-truncate" style="max-width: 140px;">{{ Auth::user()->nama_user }}</span>
                                <span class="role-badge {{ Auth::user()->isPenjual() ? 'role-badge-penjual' : 'role-badge-pembeli' }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 mt-2 py-2" aria-labelledby="userMenu" style="min-width: 210px;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold text-dark text-truncate">{{ Auth::user()->nama_user }}</div>
                                    <small class="text-muted text-truncate d-block">{{ Auth::user()->email }}</small>
                                </li>
                                @if(Auth::user()->isPenjual())
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi-speedometer2 me-2 text-dark"></i>Panel AdminLTE</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.produk.index') }}"><i class="bi-boxes me-2 text-dark"></i>Kelola Produk</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.orders.index') }}"><i class="bi-receipt me-2 text-dark"></i>Kelola Pesanan</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                @else
                                    <li><a class="dropdown-item py-2" href="{{ route('cart.index') }}"><i class="bi-cart3 me-2 text-dark"></i>Keranjang Belanja</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('shop.orders') }}"><i class="bi-bag-check me-2 text-dark"></i>Riwayat Pesanan</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav-guest-login">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-nav-guest-register">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Global Alert Notifikasi --}}
    @if(session('success') || session('error'))
        <div class="container px-4 px-lg-5 mt-3">
            @if(session('success'))
                <div class="alert alert-dark alert-dismissible fade show border-0 text-white shadow-sm" role="alert" style="background-color: #0f172a;">
                    <i class="bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    @endif

    <!-- Page Content -->
    @yield('content')

    <footer class="py-5 bg-dark">
        <div class="container text-center text-white">
            <div class="mb-3">
                <img src="{{ asset('images/logo-white.png') }}" alt="Tokomedia" height="32" class="d-inline-block">
            </div>
            <p class="m-0 mb-1">Copyright &copy; Tokomedia 2026</p>
            <small class="text-white-50">
                <a href="{{ route('shop.index') }}" class="text-white-50 text-decoration-none me-3">Beranda</a>
                <a href="{{ route('cart.index') }}" class="text-white-50 text-decoration-none me-3">Keranjang Belanja</a>
                @guest
                    <a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Login Petugas</a>
                @endguest
            </small>
        </div>
    </footer>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="{{ asset('js/scripts.js') }}"></script>

    @stack('scripts')
</body>
</html>
