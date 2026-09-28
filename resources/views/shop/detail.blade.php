@extends('layouts.frontend')

@section('title', $produk->nama_product . ' - Tokomedia')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="bg-light py-3 border-bottom">
    <div class="container px-4 px-lg-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama_product }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Detail Section -->
<section class="py-5">
    <div class="container px-4 px-lg-5">
        <div class="row gx-5">
            <!-- Product Image -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <img src="{{ $produk->gambar_product ?: 'https://dummyimage.com/600x500/dee2e6/6c757d.jpg' }}" 
                         alt="{{ $produk->nama_product }}" 
                         class="img-fluid w-100" 
                         style="max-height: 480px; object-fit: cover;" />
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <div class="ps-md-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $produk->kategori ?? 'Umum' }}</span>
                        <span class="badge {{ $produk->stok > 0 ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill">
                            {{ $produk->stok > 0 ? 'Stok Tersedia (' . $produk->stok . ')' : 'Stok Habis' }}
                        </span>
                    </div>

                    <h1 class="display-6 fw-bold text-dark mb-3">{{ $produk->nama_product }}</h1>

                    <div class="mb-4">
                        <span class="display-6 fw-bold text-dark">{{ $produk->harga_rupiah }}</span>
                    </div>

                    <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                        <div class="small text-muted mb-1">Penjual / Toko:</div>
                        <div class="fw-semibold text-dark d-flex align-items-center">
                            <i class="bi-patch-check-fill text-dark me-2 fs-5"></i>
                            {{ $produk->penjual->nama_user ?? 'Admin Toko' }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-2">Deskripsi Produk:</h5>
                        <p class="text-secondary leading-relaxed" style="white-space: pre-line;">
                            {{ $produk->deskripsi ?: 'Tidak ada deskripsi rinci untuk produk ini.' }}
                        </p>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        @if($produk->stok > 0)
                            <form action="{{ route('cart.add', $produk->id_product) }}" method="POST" class="d-inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-dark btn-lg px-4 fw-semibold rounded-pill">
                                    <i class="bi-cart-plus me-1"></i> + Keranjang
                                </button>
                            </form>
                        @endif

                        @auth
                            @if($produk->stok > 0)
                                <button type="button" class="btn btn-dark btn-lg px-4 fw-semibold shadow-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#checkoutModal">
                                    <i class="bi-bag-check-fill me-2"></i> Beli Langsung
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary btn-lg px-4 rounded-pill" disabled>
                                    <i class="bi-x-circle me-1"></i> Stok Habis
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-dark btn-lg px-4 fw-semibold shadow-sm rounded-pill">
                                <i class="bi-box-arrow-in-right me-2"></i> Login untuk Membeli
                            </a>
                        @endauth
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-lg px-3 rounded-pill">
                            <i class="bi-arrow-left me-1"></i> Katalog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Checkout Modal --}}
@auth
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="checkoutModalLabel">
                    <i class="bi-bag-check-fill me-2"></i>Konfirmasi Pemesanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('order.checkout', $produk->id_product) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Ringkasan Produk --}}
                    <div class="d-flex align-items-center bg-light p-3 rounded-3 mb-3 border">
                        <img src="{{ $produk->gambar_product ?: 'https://dummyimage.com/100x100/dee2e6/6c757d.jpg' }}" 
                             alt="{{ $produk->nama_product }}" 
                             class="rounded-3 me-3" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1 text-dark">{{ $produk->nama_product }}</h6>
                            <div class="text-dark fw-bold">{{ $produk->harga_rupiah }}</div>
                        </div>
                    </div>

                    {{-- Informasi Alur Pembayaran --}}
                    <div class="alert alert-secondary py-2 px-3 mb-3 small bg-light border">
                        <div class="fw-bold mb-1 text-dark"><i class="bi-info-circle-fill me-1"></i> Informasi Pembayaran:</div>
                        <div class="text-muted">Pesanan akan dibuat terlebih dahulu. Anda dapat mentransfer pembayaran dan mengunggah bukti bayar langsung setelah ini di menu <b>Pesanan Saya</b>.</div>
                    </div>

                    {{-- Input Alamat Pengiriman --}}
                    <div class="mb-2">
                        <label for="alamat_pengiriman" class="form-label fw-bold small text-dark">
                            Alamat Lengkap Pengiriman <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="alamat_pengiriman" 
                                  name="alamat_pengiriman" 
                                  rows="3" 
                                  placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, kode pos..." 
                                  required>{{ old('alamat_pengiriman') }}</textarea>
                        <small class="text-muted">Pastikan alamat jelas agar pesanan sampai ke tujuan Anda.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 rounded-pill">
                        <i class="bi-bag-check-fill me-1"></i> Buat Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<!-- Related Products -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<section class="py-5 bg-light border-top">
    <div class="container px-4 px-lg-5">
        <h3 class="fw-bold mb-4">Produk Serupa</h3>
        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-4">
            @foreach($relatedProducts as $rel)
                <div class="col mb-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <img src="{{ $rel->gambar_product ?: 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg' }}" 
                             alt="{{ $rel->nama_product }}" 
                             class="card-img-top" 
                             style="height: 160px; object-fit: cover;" />
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-truncate mb-1">{{ $rel->nama_product }}</h6>
                            <div class="text-primary fw-bold">{{ $rel->harga_rupiah }}</div>
                        </div>
                        <div class="card-footer p-2 bg-transparent border-0">
                            <a href="{{ route('shop.detail', $rel->id_product) }}" class="btn btn-outline-dark btn-sm w-100">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
