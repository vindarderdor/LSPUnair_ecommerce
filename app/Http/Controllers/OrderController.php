<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Proses Pemesanan / Checkout Produk (Tahap 1: Membuat Pesanan)
     */
    public function checkout(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        // Pastikan stok mencukupi
        if ($produk->stok < 1 || $produk->status_product !== 'tersedia') {
            return back()->with('error', 'Maaf, stok produk ini sudah habis.');
        }

        // Validasi input: hanya alamat_pengiriman yang wajib di tahap ini
        $request->validate([
            'alamat_pengiriman' => 'required|string|min:10',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'alamat_pengiriman.required' => 'Alamat pengiriman wajib diisi.',
            'alamat_pengiriman.min' => 'Alamat pengiriman minimal 10 karakter lengkap.',
            'bukti_bayar.image' => 'File bukti transfer harus berupa gambar (JPG, PNG).',
            'bukti_bayar.max' => 'Ukuran file bukti transfer maksimal 2MB.',
        ]);

        // Simpan file bukti transfer jika diunggah bersamaan
        $namaFileBukti = null;
        $statusOrder = 'menunggu_bayar';

        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $namaFileBukti = 'bukti_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/bukti_bayar'), $namaFileBukti);
            $statusOrder = 'menunggu_konfirmasi';
        }

        // Buat record pesanan baru
        $order = Order::create([
            'pembeli_id' => Auth::id(),
            'id_product' => $produk->id_product,
            'tgl_order' => now(),
            'total_harga' => $produk->harga,
            'status_order' => $statusOrder,
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'bukti_bayar' => $namaFileBukti,
        ]);

        // Kurangi stok produk
        $produk->decrement('stok', 1);
        if ($produk->stok <= 0) {
            $produk->update(['status_product' => 'habis']);
        }

        $noOrder = 'ORD-' . str_pad($order->id_order, 4, '0', STR_PAD_LEFT);

        if ($statusOrder === 'menunggu_bayar') {
            $pesan = "Pesanan {$noOrder} berhasil dibuat! Silakan transfer pembayaran dan unggah bukti transfer Anda.";
        } else {
            $pesan = "Pesanan {$noOrder} berhasil dibuat! Penjual akan segera memverifikasi bukti pembayaran Anda.";
        }

        return redirect()->route('shop.orders')->with('success', $pesan);
    }

    /**
     * Unggah Bukti Transfer Pembayaran (Tahap 2: Setelah Pesanan Dibuat)
     */
    public function uploadBukti(Request $request, $id)
    {
        $order = Order::where('id_order', $id)
            ->where('pembeli_id', Auth::id())
            ->firstOrFail();

        if ($order->status_order === 'dibatalkan') {
            return back()->with('error', 'Pesanan ini sudah dibatalkan dan tidak dapat dibayar lagi.');
        }

        if ($order->status_order === 'selesai') {
            return back()->with('error', 'Pesanan ini sudah selesai.');
        }

        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'bukti_bayar.required' => 'File bukti transfer wajib diunggah.',
            'bukti_bayar.image' => 'File bukti transfer harus berupa format gambar (JPG, PNG).',
            'bukti_bayar.max' => 'Ukuran file bukti transfer maksimal 2MB.',
        ]);

        // Hapus file bukti lama jika ada penggantian
        if ($order->bukti_bayar && file_exists(public_path('uploads/bukti_bayar/' . $order->bukti_bayar))) {
            @unlink(public_path('uploads/bukti_bayar/' . $order->bukti_bayar));
        }

        $file = $request->file('bukti_bayar');
        $namaFileBukti = 'bukti_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/bukti_bayar'), $namaFileBukti);

        $order->update([
            'bukti_bayar' => $namaFileBukti,
            'status_order' => 'menunggu_konfirmasi',
        ]);

        $noOrder = 'ORD-' . str_pad($order->id_order, 4, '0', STR_PAD_LEFT);

        return redirect()->route('shop.orders')->with('success', "Bukti transfer untuk pesanan {$noOrder} berhasil diunggah! Penjual akan segera memverifikasi pesanan Anda.");
    }
}
