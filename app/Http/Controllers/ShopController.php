<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * Halaman Utama / Katalog Toko Online
     */
    public function index(Request $request)
    {
        $query = Produk::query();

        // Filter berdasarkan kategori jika ada
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Pencarian nama produk
        if ($request->filled('q')) {
            $query->where('nama_product', 'like', '%' . $request->q . '%');
        }

        $produks = $query->latest('id_product')->get();
        $categories = Produk::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori');

        return view('index', compact('produks', 'categories'));
    }

    /**
     * Halaman Detail Produk
     */
    public function detail($id)
    {
        $produk = Produk::with('penjual')->findOrFail($id);
        $relatedProducts = Produk::where('id_product', '!=', $id)
            ->where('kategori', $produk->kategori)
            ->take(4)
            ->get();

        return view('shop.detail', compact('produk', 'relatedProducts'));
    }


    /**
     * Halaman Riwayat Pesanan Saya (Khusus Pembeli yang login)
     */
    public function myOrders()
    {
        $orders = Order::with('produk')
            ->where('pembeli_id', Auth::id())
            ->latest('id_order')
            ->get();

        return view('shop.orders', compact('orders'));
    }
}
