@extends('layouts.admin')

@section('title', 'Tambah Produk Baru - Admin Panel')
@section('page_title', 'Tambah Produk Baru')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Formulir Tambah Produk</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger mx-3 mt-3 mb-0">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="nama_product" class="font-weight-bold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_product" name="nama_product" value="{{ old('nama_product') }}" placeholder="Contoh: Sepatu Sneakers Sporty Pria" required autofocus>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="kategori" class="font-weight-bold">Kategori Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kategori" name="kategori" list="kategoriList" value="{{ old('kategori') }}" placeholder="Pilih atau ketik kategori baru" required>
                            <datalist id="kategoriList">
                                @foreach($categories ?? [] as $kat)
                                    <option value="{{ $kat }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="status_product" class="font-weight-bold">Status Produk</label>
                            <select class="form-control" id="status_product" name="status_product">
                                <option value="tersedia" {{ old('status_product') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="habis" {{ old('status_product') == 'habis' ? 'selected' : '' }}>Habis</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="harga" class="font-weight-bold">Harga Produk (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" class="form-control" id="harga" name="harga" value="{{ old('harga') }}" min="1000" step="500" placeholder="Contoh: 150000" required>
                            </div>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="stok" class="font-weight-bold">Jumlah Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok" name="stok" value="{{ old('stok', 10) }}" min="0" placeholder="Contoh: 25" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="gambar_file" class="font-weight-bold">Upload Foto Produk (Gambar Lokal)</label>
                        <input type="file" class="form-control-file" id="gambar_file" name="gambar_file" accept="image/jpeg,image/png,image/jpg,image/webp">
                        <small class="form-text text-muted">Format: JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="gambar_url" class="font-weight-bold">Atau Gunakan Link URL Gambar Eksternal</label>
                        <input type="url" class="form-control" id="gambar_url" name="gambar_url" value="{{ old('gambar_url') }}" placeholder="https://images.unsplash.com/...">
                        <small class="form-text text-muted">Bisa digunakan jika tidak ingin mengupload file lokal.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="deskripsi" class="font-weight-bold">Deskripsi Lengkap Produk</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan spesifikasi, bahan, ukuran, dan keunggulan produk...">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-success font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Produk Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
