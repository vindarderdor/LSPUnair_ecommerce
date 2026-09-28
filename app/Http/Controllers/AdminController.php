<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Dashboard Ringkasan
     */
    public function dashboard()
    {
        $totalProduk = Produk::count();
        $totalOrder = Order::count();
        $totalPembeli = User::where('role', 'pembeli')->count();
        $totalPendapatan = Order::where('status_order', 'selesai')->sum('total_harga');
        $recentOrders = Order::with(['pembeli', 'produk'])->latest('id_order')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProduk',
            'totalOrder',
            'totalPembeli',
            'totalPendapatan',
            'recentOrders'
        ));
    }

    /**
     * Halaman Kelola Data Produk
     */
    public function produk(Request $request)
    {
        $query = Produk::with('penjual');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $query->where('nama_product', 'like', '%' . $request->q . '%');
        }

        $produks = $query->latest('id_product')->paginate(10);
        $categories = Produk::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori');

        return view('admin.produk.index', compact('produks', 'categories'));
    }

    /**
     * Halaman Form Tambah Produk
     */
    public function createProduk()
    {
        $categories = Produk::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori');
        return view('admin.produk.create', compact('categories'));
    }

    /**
     * Simpan Produk Baru ke Database
     */
    public function storeProduk(Request $request)
    {
        $request->validate([
            'nama_product' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status_product' => 'required|in:tersedia,habis',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gambar_url' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_product.required' => 'Nama produk wajib diisi.',
            'kategori.required' => 'Kategori produk wajib diisi.',
            'harga.required' => 'Harga produk wajib diisi.',
            'stok.required' => 'Jumlah stok wajib diisi.',
            'gambar_file.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'gambar_file.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $gambarPath = 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg';

        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            $filename = 'prod_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/produk'), $filename);
            $gambarPath = asset('uploads/produk/' . $filename);
        } elseif ($request->filled('gambar_url')) {
            $gambarPath = $request->gambar_url;
        }

        Produk::create([
            'penjual_id' => Auth::id(),
            'nama_product' => $request->nama_product,
            'kategori' => $request->kategori,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'status_product' => $request->status_product,
            'gambar_product' => $gambarPath,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.produk.index')->with('success', 'Produk baru berhasil ditambahkan!');
    }

    /**
     * Halaman Form Edit Produk
     */
    public function editProduk($id)
    {
        $produk = Produk::findOrFail($id);
        $categories = Produk::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori');

        return view('admin.produk.edit', compact('produk', 'categories'));
    }

    /**
     * Perbarui Data Produk di Database
     */
    public function updateProduk(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama_product' => 'required|string|max:150',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status_product' => 'required|in:tersedia,habis',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gambar_url' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_product.required' => 'Nama produk wajib diisi.',
            'kategori.required' => 'Kategori produk wajib diisi.',
            'harga.required' => 'Harga produk wajib diisi.',
            'stok.required' => 'Jumlah stok wajib diisi.',
            'gambar_file.image' => 'File harus berupa gambar (JPG, PNG, WEBP).',
            'gambar_file.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $gambarPath = $produk->gambar_product;

        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            $filename = 'prod_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/produk'), $filename);
            $gambarPath = asset('uploads/produk/' . $filename);
        } elseif ($request->filled('gambar_url')) {
            $gambarPath = $request->gambar_url;
        }

        $produk->update([
            'nama_product' => $request->nama_product,
            'kategori' => $request->kategori,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'status_product' => $request->status_product,
            'gambar_product' => $gambarPath,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.produk.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    /**
     * Hapus Produk dari Database
     */
    public function destroyProduk($id)
    {
        $produk = Produk::findOrFail($id);

        // Cek apakah produk memiliki riwayat transaksi/pesanan (FK Restrict)
        if ($produk->orders()->exists()) {
            $produk->update([
                'status_product' => 'habis',
                'stok' => 0,
            ]);
            return redirect()->route('admin.produk.index')->with('error', 'Produk memiliki riwayat pesanan sehingga tidak dapat dihapus permanen. Status produk diubah menjadi Habis.');
        }

        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus dari sistem!');
    }

    /**
     * Halaman Kelola Transaksi & Pesanan
     */
    public function orders(Request $request)
    {
        $query = Order::with(['pembeli', 'produk']);

        if ($request->filled('status')) {
            $query->where('status_order', $request->status);
        }

        $orders = $query->latest('id_order')->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Halaman Data Pengguna (Pembeli & Penjual)
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest('id_user')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Perbarui Status Pesanan Pelanggan (Konfirmasi/Kirim/Selesai/Batalkan)
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::with('produk')->findOrFail($id);

        $request->validate([
            'status_order' => 'required|in:menunggu_bayar,menunggu_konfirmasi,diproses,dikirim,selesai,dibatalkan',
        ], [
            'status_order.required' => 'Status pesanan wajib dipilih.',
            'status_order.in' => 'Status pesanan tidak valid.',
        ]);

        $statusLama = $order->status_order;
        $statusBaru = $request->status_order;

        // Jika pesanan dibatalkan, kembalikan stok produk
        if ($statusBaru === 'dibatalkan' && $statusLama !== 'dibatalkan') {
            if ($order->produk) {
                $order->produk->increment('stok', 1);
                if ($order->produk->status_product === 'habis') {
                    $order->produk->update(['status_product' => 'tersedia']);
                }
            }
        }

        // Jika sebelumnya dibatalkan lalu diaktifkan kembali, kurangi stok kembali jika ada
        if ($statusLama === 'dibatalkan' && $statusBaru !== 'dibatalkan') {
            if ($order->produk && $order->produk->stok > 0) {
                $order->produk->decrement('stok', 1);
                if ($order->produk->stok <= 0) {
                    $order->produk->update(['status_product' => 'habis']);
                }
            }
        }

        $order->update(['status_order' => $statusBaru]);

        $noOrder = 'ORD-' . str_pad($order->id_order, 4, '0', STR_PAD_LEFT);
        $statusLabels = [
            'menunggu_bayar' => 'Menunggu Bayar',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        $label = $statusLabels[$statusBaru] ?? $statusBaru;

        return back()->with('success', "Status pesanan {$noOrder} berhasil diperbarui menjadi: {$label}.");
    }

    /**
     * Ubah Role Pengguna (Pembeli <-> Penjual)
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:pembeli,penjual',
        ]);

        $user->update([
            'role' => $request->role,
        ]);

        $roleLabel = $request->role === 'penjual' ? 'Penjual (Admin)' : 'Pembeli';

        return back()->with('success', "Role pengguna '{$user->nama_user}' berhasil diubah menjadi: {$roleLabel}.");
    }
}
