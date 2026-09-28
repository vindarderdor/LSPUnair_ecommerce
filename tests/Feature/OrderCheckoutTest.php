<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_checkout(): void
    {
        $produk = Produk::first();
        $response = $this->post('/pesan/' . $produk->id_product, [
            'alamat_pengiriman' => 'Jl. Kebon Jeruk No. 10 Jakarta Barat',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_buyer_can_create_order_without_uploading_payment_proof(): void
    {
        $pembeli = User::where('email', 'pembeli@gmail.com')->first();
        $this->assertNotNull($pembeli);

        $produk = Produk::where('stok', '>', 0)->first();
        $this->assertNotNull($produk);

        $initialStock = $produk->stok;

        // Tahap 1: Pembeli membuat pesanan terlebih dahulu tanpa bukti bayar
        $response = $this->actingAs($pembeli)->post('/pesan/' . $produk->id_product, [
            'alamat_pengiriman' => 'Jl. Merdeka No. 45, Kecamatan Gambir, Jakarta Pusat',
        ]);

        $response->assertRedirect('/pesanan-saya');
        $response->assertSessionHas('success');

        // Verifikasi data pesanan berstatus 'menunggu_bayar' dan bukti_bayar bernilai null
        $this->assertDatabaseHas('orders', [
            'pembeli_id' => $pembeli->id_user,
            'id_product' => $produk->id_product,
            'status_order' => 'menunggu_bayar',
            'bukti_bayar' => null,
            'alamat_pengiriman' => 'Jl. Merdeka No. 45, Kecamatan Gambir, Jakarta Pusat',
        ]);

        // Verifikasi stok berkurang 1
        $produk->refresh();
        $this->assertEquals($initialStock - 1, $produk->stok);
    }

    public function test_buyer_can_upload_payment_proof_after_order_created(): void
    {
        $pembeli = User::where('email', 'pembeli@gmail.com')->first();
        $this->assertNotNull($pembeli);

        $produk = Produk::where('stok', '>', 0)->first();

        // Buat pesanan dengan status menunggu_bayar
        $order = Order::create([
            'pembeli_id' => $pembeli->id_user,
            'id_product' => $produk->id_product,
            'tgl_order' => now(),
            'total_harga' => $produk->harga,
            'status_order' => 'menunggu_bayar',
            'alamat_pengiriman' => 'Jl. Raya Darmo No. 25, Surabaya',
            'bukti_bayar' => null,
        ]);

        // Tahap 2: Pembeli mengunggah bukti bayar setelah order dibuat
        $response = $this->actingAs($pembeli)->post('/pesanan/' . $order->id_order . '/bukti', [
            'bukti_bayar' => UploadedFile::fake()->image('bukti_transfer_pembeli.jpg'),
        ]);

        $response->assertRedirect('/pesanan-saya');
        $response->assertSessionHas('success');

        // Verifikasi status order berubah menjadi 'menunggu_konfirmasi'
        $order->refresh();
        $this->assertEquals('menunggu_konfirmasi', $order->status_order);
        $this->assertNotNull($order->bukti_bayar);

        // Hapus file temporary yang mungkin terbuat oleh UploadedFile::fake()
        if ($order->bukti_bayar && file_exists(public_path('uploads/bukti_bayar/' . $order->bukti_bayar))) {
            @unlink(public_path('uploads/bukti_bayar/' . $order->bukti_bayar));
        }
    }
}
