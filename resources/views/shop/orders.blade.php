@extends('layouts.frontend')

@section('title', 'Riwayat Pesanan Saya - Tokomedia')

@section('content')
<div class="bg-light py-4 border-bottom">
    <div class="container px-4 px-lg-5">
        <h2 class="fw-bold mb-1 text-dark"><i class="bi-bag-check me-2 text-dark"></i>Pesanan Saya</h2>
        <p class="text-muted small mb-0">Pantau status transaksi, lakukan pembayaran, dan lihat status pengiriman</p>
    </div>
</div>

<div class="container px-4 px-lg-5 py-5">
    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi-exclamation-octagon-fill me-2 fs-5"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
            <i class="bi-exclamation-circle-fill me-2"></i>
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($orders->count() > 0)
        @php
            $unpaidCount = $orders->where('status_order', 'menunggu_bayar')->count();
        @endphp

        @if($unpaidCount > 0)
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 shadow-sm border-0" role="alert">
                <i class="bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
                <div class="flex-grow-1">
                    <strong class="d-block">Perhatian: Ada {{ $unpaidCount }} pesanan menunggu pembayaran!</strong>
                    <div class="small">Silakan selesaikan pembayaran ke rekening toko dan klik tombol <b>Unggah Bukti</b> pada pesanan Anda.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4">No. Order</th>
                            <th class="py-3">Produk</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Total Tagihan</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 px-4 text-center" style="min-width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="px-4 fw-bold text-secondary">
                                    ORD-{{ str_pad($order->id_order, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $order->produk->gambar_product ?: 'https://dummyimage.com/100x100/dee2e6/6c757d.jpg' }}" 
                                             alt="{{ $order->produk->nama_product ?? 'Produk' }}" 
                                             class="rounded-3 me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $order->produk->nama_product ?? 'Produk Dihapus' }}</div>
                                            <small class="text-muted">{{ $order->produk->kategori ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    {{ $order->tgl_order ? $order->tgl_order->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="fw-bold text-dark">
                                    {{ $order->total_harga_rupiah }}
                                </td>
                                <td>
                                    {!! $order->status_badge !!}
                                </td>
                                <td class="px-4 text-center">
                                    @if($order->status_order === 'menunggu_bayar')
                                        <button type="button" 
                                                class="btn btn-dark text-white btn-sm rounded-pill px-3 fw-semibold shadow-sm"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadModal-{{ $order->id_order }}">
                                            <i class="bi-upload me-1"></i> Unggah Bukti
                                        </button>
                                    @elseif($order->bukti_bayar)
                                        <a href="{{ asset('uploads/bukti_bayar/' . $order->bukti_bayar) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-2 me-1" title="Lihat Bukti Bayar">
                                            <i class="bi-receipt me-1"></i> Bukti
                                        </a>
                                    @endif

                                    @if($order->produk)
                                        <a href="{{ route('shop.detail', $order->produk->id_product) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modals Upload Bukti Bayar --}}
        @foreach($orders as $order)
            @if($order->status_order === 'menunggu_bayar')
                <div class="modal fade text-start" id="uploadModal-{{ $order->id_order }}" tabindex="-1" aria-labelledby="uploadModalLabel-{{ $order->id_order }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="modal-header bg-dark text-white py-3">
                                <h5 class="modal-title fw-bold" id="uploadModalLabel-{{ $order->id_order }}">
                                    <i class="bi-upload me-2"></i>Unggah Bukti Pembayaran
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('order.upload-bukti', $order->id_order) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body p-4">
                                    {{-- Detail Order --}}
                                    <div class="bg-light p-3 rounded-3 mb-3 border">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">No. Pesanan:</span>
                                            <span class="fw-bold">ORD-{{ str_pad($order->id_order, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted small">Produk:</span>
                                            <span class="fw-semibold text-truncate ms-2 text-end">{{ $order->produk->nama_product ?? '-' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">Total Tagihan:</span>
                                            <span class="fw-bold text-dark fs-5">{{ $order->total_harga_rupiah }}</span>
                                        </div>
                                    </div>

                                    {{-- Rekening Transfer --}}
                                    <div class="alert alert-secondary py-2 px-3 mb-3 small bg-light border">
                                        <div class="fw-bold mb-1 text-dark"><i class="bi-credit-card me-1"></i> Rekening Transfer Tokomedia:</div>
                                        <div>• <b>Bank BCA</b>: 873-092-1122 a.n <i>Tokomedia</i></div>
                                        <div>• <b>Bank Mandiri</b>: 142-00-998877 a.n <i>Tokomedia</i></div>
                                    </div>

                                    {{-- File Input --}}
                                    <div class="mb-2">
                                        <label for="bukti_bayar_{{ $order->id_order }}" class="form-label fw-bold small text-dark">
                                            Pilih Foto Bukti Transfer <span class="text-danger">*</span>
                                        </label>
                                        <input class="form-control" 
                                               type="file" 
                                               id="bukti_bayar_{{ $order->id_order }}" 
                                               name="bukti_bayar" 
                                               accept="image/jpeg,image/png,image/jpg" 
                                               required>
                                        <small class="text-muted">Format JPG/PNG, ukuran maksimal 2MB.</small>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light p-3">
                                    <button type="button" class="btn btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-dark fw-bold px-4 rounded-pill">
                                        <i class="bi-cloud-arrow-up-fill me-1"></i> Kirim Bukti Transfer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

    @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <div class="display-3 text-muted mb-3"><i class="bi-cart-x"></i></div>
            <h4 class="fw-bold text-dark">Belum Ada Pesanan</h4>
            <p class="text-muted">Anda belum pernah melakukan pemesanan produk di toko kami.</p>
            <div>
                <a href="{{ route('shop.index') }}" class="btn btn-primary px-4 rounded-pill">
                    <i class="bi-bag-plus me-1"></i> Mulai Belanja Sekarang
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
