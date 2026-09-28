@extends('layouts.frontend')

@section('title', 'Keranjang Belanja - Tokomedia')

@section('content')
<section class="py-4 py-lg-5" style="background-color: #f8fafc; min-height: 80vh;">
    <div class="container px-4 px-lg-5">
        
        {{-- Flash Alerts --}}
        @if (session('success'))
            <div class="alert alert-dark alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert" style="background-color: #0f172a; color: #ffffff;">
                <i class="bi-check-circle-fill me-2 fs-5"></i>
                <div class="small">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white ms-auto shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
                <i class="bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div class="small">{{ session('error') }}</div>
                <button type="button" class="btn-close shadow-none ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi-cart3 me-2"></i>Keranjang Belanja
                </h1>
                <p class="text-muted small mb-0">Kelola dan selesaikan pesanan produk pilihan Anda</p>
            </div>
            @if(!empty($cart))
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang belanja?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="font-size: 0.8rem;">
                        <i class="bi-trash me-1"></i>Kosongkan Keranjang
                    </button>
                </form>
            @endif
        </div>

        @if(empty($cart))
            {{-- Empty State Cart ala Tokopedia (Monochrome) --}}
            <div class="card border border-slate-200 rounded-4 shadow-sm p-5 text-center bg-white my-4">
                <div class="py-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-secondary rounded-circle mb-3" style="width: 88px; height: 88px;">
                        <i class="bi-bag-x fs-1 text-muted"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Wah, keranjang belanjamu kosong</h4>
                    <p class="text-muted small mb-4 mx-auto" style="max-width: 440px;">
                        Yuk, isi dengan barang-barang pilihan Anda! Mulai belanja dan temukan berbagai produk berkualitas di katalog kami.
                    </p>
                    <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi-shop me-2"></i>Mulai Belanja Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="row g-4">
                {{-- Daftar Item Keranjang (Kiri) --}}
                <div class="col-lg-8">
                    <div class="card border border-slate-200 rounded-4 shadow-sm bg-white overflow-hidden mb-3">
                        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark small text-uppercase tracking-wider">
                                <i class="bi-bag-check me-2"></i>Daftar Produk ({{ $totalQty }} Barang)
                            </span>
                            <span class="badge bg-light text-dark border">Official Catalog</span>
                        </div>

                        <div class="card-body p-0">
                            @foreach($cart as $id => $item)
                                <div class="p-3 p-md-4 border-bottom {{ $loop->last ? 'border-bottom-0' : '' }}">
                                    <div class="row align-items-center g-3">
                                        {{-- Gambar Produk --}}
                                        <div class="col-auto">
                                            @php
                                                $itemImg = $item['gambar_product'] ?? ($item['gambar'] ?? null);
                                                if ($itemImg) {
                                                    if (!str_starts_with($itemImg, 'http://') && !str_starts_with($itemImg, 'https://')) {
                                                        $itemImg = asset($itemImg);
                                                    }
                                                } else {
                                                    $itemImg = 'https://dummyimage.com/100x100/dee2e6/6c757d.jpg';
                                                }
                                            @endphp
                                            <a href="{{ route('shop.detail', $item['id_product']) }}">
                                                <img src="{{ $itemImg }}" 
                                                     alt="{{ $item['nama_product'] }}" 
                                                     class="rounded-3 border object-fit-cover" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            </a>
                                        </div>

                                        {{-- Informasi Produk --}}
                                        <div class="col">
                                            <div class="mb-1">
                                                <span class="badge bg-light text-secondary border small" style="font-size: 0.7rem;">
                                                    {{ $item['kategori'] ?? 'Umum' }}
                                                </span>
                                            </div>
                                            <h6 class="fw-bold mb-1">
                                                <a href="{{ route('shop.detail', $item['id_product']) }}" class="text-dark text-decoration-none">
                                                    {{ $item['nama_product'] }}
                                                </a>
                                            </h6>
                                            <div class="text-dark fw-bold mb-2">
                                                Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                                <span class="text-muted fw-normal small">/ unit</span>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                {{-- Kuantitas Controls ala Tokopedia --}}
                                                <div class="d-inline-flex align-items-center border rounded-pill bg-light p-1">
                                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        <input type="hidden" name="action" value="decrease">
                                                        <button type="submit" class="btn btn-sm btn-white border-0 rounded-circle text-dark p-0 d-flex align-items-center justify-content-center" 
                                                                style="width: 26px; height: 26px;" title="Kurangi">
                                                            <i class="bi-dash"></i>
                                                        </button>
                                                    </form>

                                                    <span class="px-3 fw-bold text-dark small">{{ $item['qty'] }}</span>

                                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        <input type="hidden" name="action" value="increase">
                                                        <button type="submit" class="btn btn-sm btn-white border-0 rounded-circle text-dark p-0 d-flex align-items-center justify-content-center" 
                                                                style="width: 26px; height: 26px;" title="Tambah"
                                                                {{ $item['qty'] >= $item['stok'] ? 'disabled' : '' }}>
                                                            <i class="bi-plus"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="text-end">
                                                        <span class="text-muted small d-block" style="font-size: 0.75rem;">Subtotal</span>
                                                        <span class="fw-bold text-dark">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                                    </div>

                                                    {{-- Tombol Hapus Item --}}
                                                    <form action="{{ route('cart.remove', $id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Hapus {{ $item['nama_product'] }} dari keranjang?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-link text-danger p-1 text-decoration-none" title="Hapus produk">
                                                            <i class="bi-trash3 fs-5"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('shop.index') }}" class="btn btn-outline-dark rounded-pill px-4 py-2 small fw-semibold">
                        <i class="bi-arrow-left me-2"></i>Tambah Produk Lainnya
                    </a>
                </div>

                {{-- Ringkasan Belanja (Kanan) --}}
                <div class="col-lg-4">
                    <div class="card border border-slate-200 rounded-4 shadow-sm bg-white p-4 sticky-top" style="top: 85px;">
                        <h5 class="fw-bold text-dark mb-3">Ringkasan Belanja</h5>
                        
                        <div class="d-flex justify-content-between text-secondary small mb-2">
                            <span>Total Harga ({{ $totalQty }} barang)</span>
                            <span class="fw-semibold text-dark">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-secondary small mb-2">
                            <span>Biaya Pengiriman</span>
                            <span class="badge bg-light text-dark border">Bebas Ongkir</span>
                        </div>
                        <div class="d-flex justify-content-between text-secondary small mb-3">
                            <span>Diskon Marketplace</span>
                            <span class="text-muted">- Rp 0</span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fw-bold text-dark">Total Tagihan</span>
                            <span class="h4 fw-bold text-dark mb-0">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>

                        @auth
                            @if(Auth::user()->isPembeli())
                                <button type="button" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#checkoutCartModal">
                                    <i class="bi-credit-card me-2"></i>Checkout Pesanan ({{ $totalQty }})
                                </button>
                            @else
                                <div class="alert alert-light border small text-muted text-center mb-0">
                                    <i class="bi-info-circle me-1"></i> Akun penjual tidak dapat melakukan checkout pesanan.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-dark w-100 rounded-pill py-2 fw-bold shadow-sm text-center">
                                <i class="bi-box-arrow-in-right me-2"></i>Masuk untuk Checkout
                            </a>
                        @endauth

                        <div class="mt-4 pt-3 border-top text-center text-muted" style="font-size: 0.75rem;">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                <i class="bi-shield-check text-dark"></i>
                                <span class="fw-semibold text-dark">Transaksi Aman & Terpercaya</span>
                            </div>
                            <span>Pesanan dibuat langsung dan dapat dikonfirmasi via bukti transfer</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section>

{{-- Modal Checkout Seluruh Keranjang ala Tokopedia Monokrom --}}
@auth
    @if(!empty($cart) && Auth::user()->isPembeli())
        <div class="modal fade" id="checkoutCartModal" tabindex="-1" aria-labelledby="checkoutCartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                    <form action="{{ route('cart.checkout') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-dark text-white py-3 px-4">
                            <h5 class="modal-title fw-bold" id="checkoutCartModalLabel">
                                <i class="bi-bag-check-fill me-2"></i>Konfirmasi Checkout Keranjang
                            </h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark mb-2">Ringkasan Pesanan ({{ count($cart) }} Macam Produk)</h6>
                                <div class="border rounded-3 p-3 bg-light" style="max-height: 180px; overflow-y: auto;">
                                    @foreach($cart as $item)
                                        <div class="d-flex justify-content-between align-items-center py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            <div class="small text-truncate me-2" style="max-width: 320px;">
                                                <span class="fw-semibold text-dark">{{ $item['nama_product'] }}</span>
                                                <span class="text-muted">x {{ $item['qty'] }}</span>
                                            </div>
                                            <span class="small fw-bold text-dark">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                    <div class="d-flex justify-content-between align-items-center pt-2 mt-1 border-top fw-bold text-dark">
                                        <span>Total Pembayaran:</span>
                                        <span class="fs-6">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="cart_alamat_pengiriman" class="form-label fw-bold text-dark">
                                    Alamat Pengiriman Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea name="alamat_pengiriman" 
                                          id="cart_alamat_pengiriman" 
                                          rows="3" 
                                          class="form-control rounded-3 shadow-none border-secondary-subtle" 
                                          placeholder="Masukkan nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten, dan kode pos..." 
                                          required minlength="10">{{ old('alamat_pengiriman') }}</textarea>
                                <div class="form-text small">Pastikan alamat jelas dan lengkap agar proses pengiriman lancar.</div>
                            </div>

                            {{-- Petunjuk Rekening Pembayaran --}}
                            <div class="p-3 bg-light rounded-3 border mb-4">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi-bank text-dark fs-5 me-2"></i>
                                    <span class="fw-bold text-dark small">Rekening Pembayaran Toko:</span>
                                </div>
                                <div class="row g-2 small text-secondary">
                                    <div class="col-sm-6">
                                        <div class="p-2 bg-white rounded border">
                                            <strong>BCA:</strong> 123-456-7890<br>
                                            <span class="text-muted">a.n. Tokomedia Official</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="p-2 bg-white rounded border">
                                            <strong>Mandiri:</strong> 987-654-3210<br>
                                            <span class="text-muted">a.n. Tokomedia Official</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Alur Revisi: Unggah Bukti Bayar Bersamaan atau Nanti --}}
                            <div class="mb-3">
                                <label for="cart_bukti_bayar" class="form-label fw-bold text-dark">
                                    Unggah Bukti Transfer <span class="badge bg-light text-muted border fw-normal">Opsional</span>
                                </label>
                                <input type="file" 
                                       name="bukti_bayar" 
                                       id="cart_bukti_bayar" 
                                       class="form-control rounded-3 shadow-none" 
                                       accept="image/jpeg,image/png,image/jpg">
                                <div class="form-text small text-muted">
                                    <i class="bi-info-circle me-1"></i>Anda dapat langsung membuat pesanan sekarang dan mengunggah bukti bayar nanti di halaman <strong>Pesanan Saya</strong>.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top py-3 px-4">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold">
                                <i class="bi-check2-circle me-1"></i>Buat Pesanan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth
@endsection
