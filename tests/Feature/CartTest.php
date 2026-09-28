<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CartTest extends TestCase
{
    use DatabaseTransactions;

    public function test_cart_page_shows_empty_state_when_no_items(): void
    {
        $response = $this->withSession([])->get('/keranjang');
        $response->assertStatus(200);
        $response->assertSee('Wah, keranjang belanjamu kosong');
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $produk = Produk::where('stok', '>', 0)->first();
        $this->assertNotNull($produk);

        $response = $this->post('/keranjang/tambah/' . $produk->id_product, [
            'qty' => 2,
        ]);

        $response->assertSessionHas('cart');
        $cart = session('cart');
        $this->assertArrayHasKey($produk->id_product, $cart);
        $this->assertEquals(2, $cart[$produk->id_product]['qty']);
    }

    public function test_user_can_update_cart_quantity(): void
    {
        $produk = Produk::where('stok', '>', 2)->first();
        $this->assertNotNull($produk);

        $initialCart = [
            $produk->id_product => [
                'id_product' => $produk->id_product,
                'nama_product' => $produk->nama_product,
                'harga' => $produk->harga,
                'gambar' => $produk->gambar,
                'kategori' => $produk->kategori,
                'stok' => $produk->stok,
                'qty' => 1,
                'subtotal' => $produk->harga,
            ],
        ];

        $response = $this->withSession(['cart' => $initialCart])
            ->post('/keranjang/update/' . $produk->id_product, [
                'action' => 'increase',
            ]);

        $response->assertRedirect('/keranjang');
        $cart = session('cart');
        $this->assertEquals(2, $cart[$produk->id_product]['qty']);
    }

    public function test_user_can_remove_product_from_cart(): void
    {
        $produk = Produk::first();
        $this->assertNotNull($produk);

        $initialCart = [
            $produk->id_product => [
                'id_product' => $produk->id_product,
                'nama_product' => $produk->nama_product,
                'harga' => $produk->harga,
                'gambar' => $produk->gambar,
                'kategori' => $produk->kategori,
                'stok' => $produk->stok,
                'qty' => 1,
                'subtotal' => $produk->harga,
            ],
        ];

        $response = $this->withSession(['cart' => $initialCart])
            ->post('/keranjang/hapus/' . $produk->id_product);

        $response->assertRedirect('/keranjang');
        $cart = session('cart');
        $this->assertArrayNotHasKey($produk->id_product, $cart);
    }

    public function test_buyer_can_checkout_cart_successfully(): void
    {
        $pembeli = User::where('role', 'pembeli')->first();
        $produk = Produk::where('stok', '>', 1)->first();
        $this->assertNotNull($pembeli);
        $this->assertNotNull($produk);

        $cart = [
            $produk->id_product => [
                'id_product' => $produk->id_product,
                'nama_product' => $produk->nama_product,
                'harga' => $produk->harga,
                'gambar' => $produk->gambar,
                'kategori' => $produk->kategori,
                'stok' => $produk->stok,
                'qty' => 1,
                'subtotal' => $produk->harga,
            ],
        ];

        $response = $this->actingAs($pembeli)
            ->withSession(['cart' => $cart])
            ->post('/keranjang/checkout', [
                'alamat_pengiriman' => 'Jl. Pahlawan Belanja No. 10, Jakarta Selatan',
            ]);

        $response->assertRedirect(route('shop.orders'));
        $this->assertEmpty(session('cart', []));

        $this->assertDatabaseHas('orders', [
            'pembeli_id' => $pembeli->id_user,
            'id_product' => $produk->id_product,
            'status_order' => 'menunggu_bayar',
            'alamat_pengiriman' => 'Jl. Pahlawan Belanja No. 10, Jakarta Selatan',
        ]);
    }
}
