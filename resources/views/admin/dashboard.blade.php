@extends('layouts.admin')

@section('title', 'Dashboard Admin - E-Commerce')
@section('page_title', 'Ringkasan Dashboard')

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalOrder ?? 0 }}</h3>
                <p>Total Pesanan</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <a href="#" class="small-box-footer">Pesanan Toko <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
                <p>Pendapatan (Selesai)</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="#" class="small-box-footer">Laporan Keuangan <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalPembeli ?? 0 }}</h3>
                <p>Pelanggan Terdaftar</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="#" class="small-box-footer">Data Pengguna <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalProduk ?? 0 }}</h3>
                <p>Total Produk Aktif</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
            <a href="#" class="small-box-footer">Katalog Produk <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-8 connectedSortable">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title font-weight-bold">Pesanan Terbaru Masuk</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0 table-hover">
                        <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentOrders ?? [] as $order)
                            <tr>
                                <td>
                                    <span class="badge badge-light font-weight-bold">
                                        ORD-{{ str_pad($order->id_order, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>{{ $order->pembeli->nama_user ?? 'Pembeli' }}</td>
                                <td>{{ $order->produk->nama_product ?? '-' }}</td>
                                <td class="font-weight-bold text-success">{{ $order->total_harga_rupiah }}</td>
                                <td>{!! $order->status_badge !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data pesanan masuk.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                <span class="text-muted small">Data pesanan terhubung langsung secara dinamis dengan database.</span>
            </div>
        </div>
    </section>

    <!-- Right col -->
    <section class="col-lg-4 connectedSortable">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Status Sistem E-Commerce</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <h5><i class="icon fas fa-check"></i> Database Terhubung</h5>
                    Tabel: <b>users</b>, <b>produk</b>, <b>orders</b> aktif dan sinkron.
                </div>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Produk Tersedia
                        <span class="badge badge-primary badge-pill">{{ $totalProduk ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Pesanan
                        <span class="badge badge-info badge-pill">{{ $totalOrder ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Akun Pembeli
                        <span class="badge badge-warning badge-pill">{{ $totalPembeli ?? 0 }}</span>
                    </li>
                </ul>
                <a href="{{ route('shop.index') }}" target="_blank" class="btn btn-primary btn-block">
                    <i class="fas fa-store mr-1"></i> Buka Halaman Toko
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
