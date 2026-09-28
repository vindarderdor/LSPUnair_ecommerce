@extends('layouts.admin')

@section('title', 'Data Pengguna - Admin Panel')
@section('page_title', 'Data Pengguna')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Daftar Pengguna Terdaftar</h3>
                <div class="card-tools">
                    <span class="badge badge-warning">Total: {{ $users->total() }} User</span>
                </div>
            </div>

            <div class="card-body">
                {{-- Filter Role --}}
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ !request('role') ? 'btn-dark' : 'btn-outline-dark' }} mr-1">Semua</a>
                    <a href="{{ route('admin.users.index', ['role' => 'pembeli']) }}" class="btn btn-sm {{ request('role') == 'pembeli' ? 'btn-info' : 'btn-outline-info' }} mr-1">Pembeli</a>
                    <a href="{{ route('admin.users.index', ['role' => 'penjual']) }}" class="btn btn-sm {{ request('role') == 'penjual' ? 'btn-success' : 'btn-outline-success' }} mr-1">Penjual</a>
                </div>

                {{-- Table Users --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th style="width: 50px;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th style="width: 120px;">Role</th>
                                <th style="width: 140px;">Tgl Daftar</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 180px;">Aksi / Ubah Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $u)
                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $users->firstItem() + $index }}
                                    </td>
                                    <td class="align-middle font-weight-bold">
                                        <i class="fas fa-user-circle mr-2 text-secondary"></i>
                                        {{ $u->nama_user }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $u->email }}
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($u->role == 'penjual')
                                            <span class="badge badge-success px-2 py-1">Penjual / Admin</span>
                                        @else
                                            <span class="badge badge-info px-2 py-1">Pembeli</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle text-muted small">
                                        {{ $u->tgl_daftar ? $u->tgl_daftar->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge {{ $u->status_user == 'aktif' ? 'badge-primary' : 'badge-danger' }}">
                                            {{ ucfirst($u->status_user) }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if(Auth::id() == $u->id_user)
                                            <span class="badge badge-light border text-muted px-2 py-1">
                                                <i class="fas fa-lock mr-1"></i> Akun Anda
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.update-role', $u->id_user) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah role {{ $u->nama_user }} menjadi {{ $u->role === 'penjual' ? 'Pembeli' : 'Penjual' }}?')">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="role" value="{{ $u->role === 'penjual' ? 'pembeli' : 'penjual' }}">
                                                <button type="submit" class="btn btn-sm {{ $u->role === 'penjual' ? 'btn-outline-info' : 'btn-outline-success' }} px-3 py-1 font-weight-bold shadow-sm rounded-pill">
                                                    @if($u->role === 'penjual')
                                                        <i class="fas fa-user mr-1"></i> Jadi Pembeli
                                                    @else
                                                        <i class="fas fa-user-shield mr-1"></i> Jadi Penjual
                                                    @endif
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada data pengguna yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Total: {{ $users->total() }} Pengguna</small>
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
