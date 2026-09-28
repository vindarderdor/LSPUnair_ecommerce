<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PageConnectionTest extends TestCase
{
    use DatabaseTransactions;
    public function test_shop_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Semua Produk Terbaru');
    }

    public function test_shop_cart_page_is_accessible(): void
    {
        $response = $this->get('/keranjang');
        $response->assertStatus(200);
        $response->assertSee('Keranjang Belanja');
    }

    public function test_shop_detail_page_is_accessible(): void
    {
        $produk = Produk::first();
        $this->assertNotNull($produk);

        $response = $this->get('/produk/' . $produk->id_product);
        $response->assertStatus(200);
        $response->assertSee($produk->nama_product);
    }

    public function test_buyer_can_access_my_orders_page(): void
    {
        $pembeli = User::where('role', 'pembeli')->first();
        $this->assertNotNull($pembeli);

        $response = $this->actingAs($pembeli)->get('/pesanan-saya');
        $response->assertStatus(200);
        $response->assertSee('Pesanan Saya');
    }

    public function test_admin_can_access_product_list_page(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $this->assertNotNull($penjual);

        $response = $this->actingAs($penjual)->get('/admin/produk');
        $response->assertStatus(200);
        $response->assertSee('Daftar Produk Toko');
    }

    public function test_admin_can_access_create_product_page(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $this->assertNotNull($penjual);

        $response = $this->actingAs($penjual)->get('/admin/produk/tambah');
        $response->assertStatus(200);
        $response->assertSee('Formulir Tambah Produk');
    }

    public function test_admin_can_access_orders_page(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $this->assertNotNull($penjual);

        $response = $this->actingAs($penjual)->get('/admin/orders');
        $response->assertStatus(200);
        $response->assertSee('Daftar Semua Pesanan Masuk');
    }

    public function test_admin_can_access_users_page(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $this->assertNotNull($penjual);

        $response = $this->actingAs($penjual)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Daftar Pengguna Terdaftar');
    }
}
