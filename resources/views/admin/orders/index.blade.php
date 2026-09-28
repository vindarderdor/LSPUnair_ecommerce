@extends('layouts.admin')

@section('title', 'Manajemen Transaksi & Pesanan - Admin Panel')
@section('page_title', 'Kelola Pesanan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Daftar Semua Pesanan Masuk</h3>
                <div class="card-tools">
                    <span class="badge badge-info font-weight-normal">Total: {{ $orders->total() }} Pesanan</span>
                </div>
            </div>

            <div class="card-body">
                {{-- Filter Status --}}
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-dark' }} mr-1 mb-1">Semua</a>
                    <a href="{{ route('admin.orders.index', ['status' => 'menunggu_bayar']) }}" class="btn btn-sm {{ request('status') == 'menunggu_bayar' ? 'btn-secondary' : 'btn-outline-secondary' }} mr-1 mb-1">Menunggu Bayar</a>
                    <a href="{{ route('admin.orders.index', ['status' => 'menunggu_konfirmasi']) }}" class="btn btn-sm {{ request('status') == 'menunggu_konfirmasi' ? 'btn-warning' : 'btn-outline-warning' }} mr-1 mb-1">Menunggu Konfirmasi</a>
                    <a href="{{ route('admin.orders.index', ['status' => 'diproses']) }}" class="btn btn-sm {{ request('status') == 'diproses' ? 'btn-info' : 'btn-outline-info' }} mr-1 mb-1">Diproses</a>
                    <a href="{{ route('admin.orders.index', ['status' => 'dikirim']) }}" class="btn btn-sm {{ request('status') == 'dikirim' ? 'btn-primary' : 'btn-outline-primary' }} mr-1 mb-1">Dikirim</a>
                    <a href="{{ route('admin.orders.index', ['status' => 'selesai']) }}" class="btn btn-sm {{ request('status') == 'selesai' ? 'btn-success' : 'btn-outline-success' }} mr-1 mb-1">Selesai</a>
                    <a href="{{ route('admin.orders.index', ['status' => 'dibatalkan']) }}" class="btn btn-sm {{ request('status') == 'dibatalkan' ? 'btn-danger' : 'btn-outline-danger' }} mr-1 mb-1">Dibatalkan</a>
                </div>

                {{-- Table Orders --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th>No. Order</th>
                                <th>Pelanggan</th>
                                <th>Produk Dipesan</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $index => $order)
                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $orders->firstItem() + $index }}
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        ORD-{{ str_pad($order->id_order, 4, '0', STR_PAD_LEFT) }}
                                        <small class="text-muted d-block">{{ $order->tgl_order ? $order->tgl_order->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold">{{ $order->pembeli->nama_user ?? 'Pembeli' }}</div>
                                        <small class="text-muted">{{ $order->pembeli->email ?? '-' }}</small>
                                    </td>
                                    <td class="align-middle">
                                        {{ $order->produk->nama_product ?? '-' }}
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        {{ $order->total_harga_rupiah }}
                                    </td>
                                    <td class="text-center align-middle">
                                        {!! $order->status_badge !!}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($order->bukti_bayar)
                                            <a href="{{ asset('uploads/bukti_bayar/' . $order->bukti_bayar) }}" target="_blank" class="btn btn-success btn-xs mr-1" title="Lihat Bukti Transfer">
                                                <i class="fas fa-receipt"></i> Bukti
                                            </a>
                                        @endif
                                        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalOrder{{ $order->id_order }}" title="Kelola Pesanan">
                                            <i class="fas fa-tasks mr-1"></i> Kelola
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada pesanan dengan status tersebut.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Total: {{ $orders->total() }} Pesanan</small>
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals Kelola Status Pesanan --}}
@foreach($orders as $order)
<div class="modal fade" id="modalOrder{{ $order->id_order }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $order->id_order }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-weight-bold" id="modalLabel{{ $order->id_order }}">
                    Kelola Pesanan #ORD-{{ str_pad($order->id_order, 4, '0', STR_PAD_LEFT) }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.orders.update-status', $order->id_order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-3">
                    {{-- Info Pelanggan & Produk --}}
                    <div class="bg-light p-3 rounded mb-3 border">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Pelanggan:</small>
                                <strong>{{ $order->pembeli->nama_user ?? '-' }}</strong>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block">Total Pembayaran:</small>
                                <strong class="text-success">{{ $order->total_harga_rupiah }}</strong>
                            </div>
                            <div class="col-12 mb-2">
                                <small class="text-muted d-block">Produk:</small>
                                <span>{{ $order->produk->nama_product ?? 'Produk Dihapus' }}</span>
                            </div>
                            <div class="col-12 mb-2">
                                <small class="text-muted d-block">Alamat Pengiriman:</small>
                                <span class="small">{{ $order->alamat_pengiriman }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bukti Pembayaran --}}
                    <div class="mb-3">
                        <label class="font-weight-bold d-block small">Bukti Transfer Pelanggan:</label>
                        @if($order->bukti_bayar)
                            <div class="d-flex align-items-center bg-light p-2 rounded border">
                                <img src="{{ asset('uploads/bukti_bayar/' . $order->bukti_bayar) }}" 
                                     alt="Bukti Bayar" 
                                     class="img-thumbnail mr-3" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                                <div>
                                    <a href="{{ asset('uploads/bukti_bayar/' . $order->bukti_bayar) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Foto Asli
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-secondary py-2 small mb-0">
                                <i class="fas fa-info-circle mr-1"></i> Belum ada bukti transfer yang diunggah.
                            </div>
                        @endif
                    </div>

                    {{-- Pilihan Status Pesanan --}}
                    <div class="form-group mb-2">
                        <label for="status_order_{{ $order->id_order }}" class="font-weight-bold">
                            Perbarui Status Pesanan: <span class="text-danger">*</span>
                        </label>
                        <select name="status_order" id="status_order_{{ $order->id_order }}" class="form-control font-weight-bold">
                            <option value="menunggu_bayar" {{ $order->status_order == 'menunggu_bayar' ? 'selected' : '' }}>
                                ⚪ Menunggu Bayar (Belum Transfer)
                            </option>
                            <option value="menunggu_konfirmasi" {{ $order->status_order == 'menunggu_konfirmasi' ? 'selected' : '' }}>
                                🟡 Menunggu Konfirmasi (Verifikasi Pembayaran)
                            </option>
                            <option value="diproses" {{ $order->status_order == 'diproses' ? 'selected' : '' }}>
                                🔵 Diproses (Pesanan Sedang Disiapkan)
                            </option>
                            <option value="dikirim" {{ $order->status_order == 'dikirim' ? 'selected' : '' }}>
                                🚚 Dikirim (Dalam Pengiriman Kurir)
                            </option>
                            <option value="selesai" {{ $order->status_order == 'selesai' ? 'selected' : '' }}>
                                🟢 Selesai (Diterima & Transaksi Berhasil)
                            </option>
                            <option value="dibatalkan" {{ $order->status_order == 'dibatalkan' ? 'selected' : '' }}>
                                🔴 Dibatalkan (Batalkan Pesanan & Kembalikan Stok)
                            </option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
