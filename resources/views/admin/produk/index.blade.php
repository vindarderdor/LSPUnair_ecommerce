@extends('layouts.admin')

@section('title', 'Kelola Data Produk - Admin Panel')
@section('page_title', 'Data Produk')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Daftar Produk Toko</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.produk.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle mr-1"></i> Tambah Produk Baru
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Form Pencarian & Filter --}}
                <form method="GET" action="{{ route('admin.produk.index') }}" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama produk..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="kategori" class="form-control form-control-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($categories ?? [] as $kat)
                                <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.produk.index') }}" class="btn btn-light btn-sm border">Reset</a>
                    </div>
                </form>

                {{-- Tabel Data Produk --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th style="width: 80px;">Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th style="width: 80px;">Stok</th>
                                <th>Status</th>
                                <th style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produks as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $produks->firstItem() + $index }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <img src="{{ $item->gambar_product ?: 'https://dummyimage.com/100x100/dee2e6/6c757d.jpg' }}" 
                                             alt="{{ $item->nama_product }}" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        {{ $item->nama_product }}
                                        <small class="text-muted d-block">{{ Str::limit($item->deskripsi, 60) }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-info">{{ $item->kategori ?? 'Umum' }}</span>
                                    </td>
                                    <td class="align-middle font-weight-bold text-success">
                                        {{ $item->harga_rupiah }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $item->stok > 0 ? 'badge-primary' : 'badge-danger' }}">
                                            {{ $item->stok }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $item->status_product == 'tersedia' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($item->status_product) }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="{{ route('shop.detail', $item->id_product) }}" target="_blank" class="btn btn-info btn-xs mr-1" title="Lihat di Toko">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.produk.edit', $item->id_product) }}" class="btn btn-warning btn-xs mr-1" title="Edit Produk">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.produk.destroy', $item->id_product) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ addslashes($item->nama_product) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs" title="Hapus Produk">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada data produk yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Total: {{ $produks->total() }} Produk</small>
                    {{ $produks->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
