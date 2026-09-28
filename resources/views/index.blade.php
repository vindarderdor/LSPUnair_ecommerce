@extends('layouts.frontend')

@section('title', 'Tokomedia - Belanja Mudah & Cepat')

@section('content')
<div class="container px-4 px-lg-5 my-4">

    {{-- Hero / Promo Banner ala Tokopedia (Black & White Edition) --}}
    <div class="p-4 p-md-5 rounded-4 text-white position-relative overflow-hidden shadow-sm mb-4" 
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f172a 100%); border: 1px solid #1e293b;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white text-dark rounded-pill px-3 py-1.5 mb-3 fw-semibold small shadow-sm">
                    <i class="bi-stars me-1 text-warning"></i> Belanja Mudah & Cepat
                </span>
                <h1 class="display-5 fw-bold mb-2 text-white">
                    Pilihan Produk Terbaik untuk Anda
                </h1>
                <p class="text-white-50 mb-0" style="font-size: 1.05rem;">
                    Temukan berbagai produk berkualitas dengan harga terbaik, jaminan keaslian, dan transaksi aman di Tokomedia.
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end d-none d-lg-block">
                <div class="bg-white bg-opacity-10 p-4 rounded-4 border border-white border-opacity-10 d-inline-block backdrop-blur">
                    <img src="{{ asset('images/logo-white.png') }}" alt="Tokomedia" height="56" class="img-fluid">
                    <div class="small text-white-50 mt-2">Official Online Marketplace</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kategori Pilihan (Category Pills Bar ala Tokopedia) --}}
    <div class="d-flex align-items-center gap-2 overflow-auto pb-2 mb-4 scrollbar-none">
        <span class="text-muted small fw-semibold text-nowrap me-1">Kategori:</span>
        <a href="{{ route('shop.index') }}" 
           class="btn btn-sm rounded-pill px-3 text-nowrap {{ !request('kategori') ? 'btn-dark text-white shadow-sm' : 'btn-outline-secondary text-dark bg-white' }}"
           style="font-size: 0.85rem;">
            Semua Produk
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('shop.index', ['kategori' => $cat]) }}" 
               class="btn btn-sm rounded-pill px-3 text-nowrap {{ request('kategori') === $cat ? 'btn-dark text-white shadow-sm' : 'btn-outline-secondary text-dark bg-white' }}"
               style="font-size: 0.85rem;">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    {{-- Header Section Produk & Info Pencarian --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Semua Produk Terbaru</h3>
            @if(request('q') || request('kategori'))
                <div class="small text-muted">
                    Menampilkan hasil 
                    @if(request('q')) untuk pencarian <b>"{{ request('q') }}"</b> @endif
                    @if(request('kategori')) kategori <b>"{{ request('kategori') }}"</b> @endif
                    ({{ $produks->count() }} produk)
                    • <a href="{{ route('shop.index') }}" class="text-dark fw-semibold text-decoration-underline ms-1">Reset Filter</a>
                </div>
            @else
                <span class="text-muted small">Menampilkan {{ $produks->count() }} produk pilihan</span>
            @endif
        </div>
    </div>

    {{-- Grid Produk ala Tokopedia (Black & White Minimalist) --}}
    <div class="row gx-3 gx-lg-4 row-cols-2 row-cols-md-3 row-cols-xl-4">
        @forelse($produks as $produk)
            <div class="col mb-4">
                <div class="card h-100 border rounded-3 overflow-hidden product-card shadow-sm" 
                     style="border-color: #e2e8f0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    
                    {{-- Foto Produk (1:1 Ratio) --}}
                    <a href="{{ route('shop.detail', $produk->id_product) }}" class="position-relative overflow-hidden d-block bg-light" style="padding-top: 100%;">
                        <img class="position-absolute top-0 start-0 w-100 h-100" 
                             src="{{ $produk->gambar_product ?: 'https://dummyimage.com/450x450/dee2e6/6c757d.jpg' }}" 
                             alt="{{ $produk->nama_product }}"
                             style="object-fit: cover; transition: transform 0.3s ease;">
                        
                        @if($produk->kategori)
                            <span class="badge bg-dark text-white position-absolute top-0 start-0 m-2 px-2.5 py-1 rounded-pill small" 
                                  style="font-size: 0.7rem; opacity: 0.9;">
                                {{ $produk->kategori }}
                            </span>
                        @endif
                    </a>

                    {{-- Detail Produk --}}
                    <div class="card-body p-3 d-flex flex-column">
                        {{-- Judul 2 baris --}}
                        <h6 class="fw-semibold text-dark mb-1" 
                            style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6rem; font-size: 0.925rem; line-height: 1.3rem;">
                            <a href="{{ route('shop.detail', $produk->id_product) }}" class="text-decoration-none text-dark hover-underline">
                                {{ $produk->nama_product }}
                            </a>
                        </h6>

                        {{-- Harga bold hitam --}}
                        <div class="fw-bold text-dark fs-5 mt-1">
                            {{ $produk->harga_rupiah }}
                        </div>

                        {{-- Toko & Badge Official ala Tokopedia --}}
                        <div class="d-flex align-items-center gap-1 text-muted mt-2" style="font-size: 0.78rem;">
                            <i class="bi-patch-check-fill text-dark"></i>
                            <span class="text-truncate">{{ $produk->penjual->nama_user ?? 'Tokomedia Official' }}</span>
                        </div>

                        {{-- Stok Status --}}
                        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top small text-muted">
                            <span style="font-size: 0.78rem;">
                                <i class="bi-box-seam me-1"></i>Stok: <b>{{ $produk->stok }}</b>
                            </span>
                            <span class="badge {{ $produk->stok > 0 ? 'bg-light text-dark border' : 'bg-danger text-white' }} px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                                {{ $produk->stok > 0 ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="mt-3 pt-1 d-flex gap-2">
                            <a href="{{ route('shop.detail', $produk->id_product) }}" class="btn btn-outline-dark btn-sm rounded-pill flex-grow-1 fw-semibold text-truncate">
                                <i class="bi-eye me-1"></i> Detail
                            </a>
                            @if($produk->stok > 0)
                                <form action="{{ route('cart.add', $produk->id_product) }}" method="POST" class="d-inline m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-dark btn-sm rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0" style="width: 32px; height: 32px;" title="Tambah ke Keranjang">
                                        <i class="bi-cart-plus"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-light rounded-4 border">
                    <i class="bi-search display-3 text-muted mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">Produk Tidak Ditemukan</h5>
                    <p class="text-muted small">Coba cari dengan kata kunci lain atau lihat semua kategori produk yang tersedia.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4 btn-sm">
                        Lihat Semua Produk
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -10px rgba(15, 23, 42, 0.15) !important;
        border-color: #cbd5e1 !important;
    }
    .product-card:hover img {
        transform: scale(1.03);
    }
    .hover-underline:hover {
        text-decoration: underline !important;
    }
</style>
@endsection
