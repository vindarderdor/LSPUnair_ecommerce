<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Tampilkan Halaman Keranjang Belanja (Shopping Cart)
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Sinkronisasi data keranjang dengan database (cek apakah produk masih ada dan update stok)
        $updatedCart = [];
        $totalHarga = 0;
        $totalQty = 0;

        foreach ($cart as $id => $item) {
            $produk = Produk::find($id);
            if ($produk && $produk->stok > 0 && $produk->status_product === 'tersedia') {
                // Pastikan qty tidak melebihi stok yang ada
                $qty = min($item['qty'], $produk->stok);
                $updatedCart[$id] = [
                    'id_product' => $produk->id_product,
                    'nama_product' => $produk->nama_product,
                    'harga' => $produk->harga,
                    'gambar_product' => $produk->gambar_product,
                    'gambar' => $produk->gambar_product,
                    'kategori' => $produk->kategori,
                    'stok' => $produk->stok,
                    'qty' => $qty,
                    'subtotal' => $produk->harga * $qty,
                ];
                $totalHarga += $produk->harga * $qty;
                $totalQty += $qty;
            }
        }

        session()->put('cart', $updatedCart);

        return view('shop.cart', [
            'cart' => $updatedCart,
            'totalHarga' => $totalHarga,
            'totalQty' => $totalQty,
        ]);
    }

    /**
     * Tambah Produk ke Keranjang Belanja
     */
    public function add(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->stok < 1 || $produk->status_product !== 'tersedia') {
            return back()->with('error', 'Maaf, stok produk ini sedang tidak tersedia.');
        }

        $qtyToAdd = max(1, (int) $request->input('qty', 1));
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $newQty = $cart[$id]['qty'] + $qtyToAdd;
            if ($newQty > $produk->stok) {
                $newQty = $produk->stok;
                $message = "Jumlah produk di keranjang disesuaikan dengan stok maksimal ({$produk->stok} unit).";
            } else {
                $message = "Jumlah produk dalam keranjang berhasil ditambahkan.";
            }
            $cart[$id]['qty'] = $newQty;
            $cart[$id]['subtotal'] = $produk->harga * $newQty;
        } else {
            if ($qtyToAdd > $produk->stok) {
                $qtyToAdd = $produk->stok;
            }
            $cart[$id] = [
                'id_product' => $produk->id_product,
                'nama_product' => $produk->nama_product,
                'harga' => $produk->harga,
                'gambar_product' => $produk->gambar_product,
                'gambar' => $produk->gambar_product,
                'kategori' => $produk->kategori,
                'stok' => $produk->stok,
                'qty' => $qtyToAdd,
                'subtotal' => $produk->harga * $qtyToAdd,
            ];
            $message = "Produk '{$produk->nama_product}' berhasil dimasukkan ke keranjang!";
        }

        session()->put('cart', $cart);

        // Jika request menginginkan langsung ke keranjang
        if ($request->has('redirect_to_cart')) {
            return redirect()->route('cart.index')->with('success', $message);
        }

        return back()->with('success', $message);
    }

    /**
     * Perbarui Kuantitas Produk di Keranjang
     */
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        $produk = Produk::find($id);
        if (!$produk || $produk->stok < 1) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('error', 'Produk sudah tidak tersedia dan telah dihapus dari keranjang.');
        }

        $action = $request->input('action');
        $qty = (int) $request->input('qty', $cart[$id]['qty']);

        if ($action === 'increase') {
            $qty = $cart[$id]['qty'] + 1;
        } elseif ($action === 'decrease') {
            $qty = $cart[$id]['qty'] - 1;
        }

        if ($qty <= 0) {
            unset($cart[$id]);
            $message = "Produk berhasil dihapus dari keranjang.";
        } else {
            if ($qty > $produk->stok) {
                $qty = $produk->stok;
                $message = "Jumlah disesuaikan dengan stok tersedia ({$produk->stok} unit).";
            } else {
                $message = "Kuantitas keranjang berhasil diperbarui.";
            }
            $cart[$id]['qty'] = $qty;
            $cart[$id]['subtotal'] = $produk->harga * $qty;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', $message);
    }

    /**
     * Hapus Satu Item dari Keranjang
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Produk berhasil dihapus dari keranjang.');
        }

        return redirect()->route('cart.index')->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Kosongkan Seluruh Keranjang
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Keranjang belanja berhasil dikosongkan.');
    }

    /**
     * Checkout Seluruh Produk di Keranjang (Tahap 1: Pembuatan Pesanan)
     */
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $request->validate([
            'alamat_pengiriman' => 'required|string|min:10',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'alamat_pengiriman.required' => 'Alamat pengiriman wajib diisi.',
            'alamat_pengiriman.min' => 'Alamat pengiriman minimal 10 karakter lengkap.',
            'bukti_bayar.image' => 'File bukti transfer harus berupa format gambar (JPG, PNG).',
            'bukti_bayar.max' => 'Ukuran file bukti transfer maksimal 2MB.',
        ]);

        $namaFileBukti = null;
        $statusOrder = 'menunggu_bayar';

        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $namaFileBukti = 'bukti_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_bayar'), $namaFileBukti);
            $statusOrder = 'menunggu_konfirmasi';
        }

        $orderCount = 0;

        foreach ($cart as $id => $item) {
            $produk = Produk::find($id);

            // Lewati jika produk sudah tidak ada atau stok habis
            if (!$produk || $produk->stok < 1) {
                continue;
            }

            $orderQty = min($item['qty'], $produk->stok);
            $totalHargaItem = $produk->harga * $orderQty;

            Order::create([
                'pembeli_id' => Auth::id(),
                'id_product' => $produk->id_product,
                'tgl_order' => now(),
                'total_harga' => $totalHargaItem,
                'status_order' => $statusOrder,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'bukti_bayar' => $namaFileBukti,
            ]);

            // Kurangi stok
            $produk->decrement('stok', $orderQty);
            if ($produk->stok <= 0) {
                $produk->update(['status_product' => 'habis']);
            }

            $orderCount++;
        }

        // Kosongkan keranjang belanja
        session()->forget('cart');

        if ($orderCount === 0) {
            return redirect()->route('cart.index')->with('error', 'Produk di keranjang sudah habis atau tidak tersedia lagi.');
        }

        $pesan = $statusOrder === 'menunggu_bayar'
            ? "{$orderCount} pesanan berhasil dibuat dari Keranjang Belanja! Silakan transfer pembayaran dan unggah bukti transfer."
            : "{$orderCount} pesanan berhasil dibuat! Penjual akan segera memverifikasi bukti pembayaran Anda.";

        return redirect()->route('shop.orders')->with('success', $pesan);
    }
}
