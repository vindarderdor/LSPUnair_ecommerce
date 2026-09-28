<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use DatabaseTransactions;
    public function test_seller_can_create_product(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $this->assertNotNull($penjual);

        $response = $this->actingAs($penjual)->post('/admin/produk', [
            'nama_product' => 'Produk Uji Coba Baru',
            'kategori' => 'Teknologi',
            'harga' => 150000,
            'stok' => 25,
            'status_product' => 'tersedia',
            'deskripsi' => 'Deskripsi produk uji coba otomatis.',
            'gambar_file' => UploadedFile::fake()->image('produk_baru.jpg'),
        ]);

        $response->assertRedirect('/admin/produk');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('produk', [
            'nama_product' => 'Produk Uji Coba Baru',
            'kategori' => 'Teknologi',
            'harga' => 150000,
            'stok' => 25,
        ]);
    }

    public function test_seller_can_view_edit_product_page(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $produk = Produk::first();

        $response = $this->actingAs($penjual)->get('/admin/produk/' . $produk->id_product . '/edit');
        $response->assertStatus(200);
        $response->assertSee('Formulir Edit Produk');
        $response->assertSee($produk->nama_product);
    }

    public function test_seller_can_update_product(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $produk = Produk::where('nama_product', 'Produk Uji Coba Baru')->first() ?? Produk::first();

        $response = $this->actingAs($penjual)->put('/admin/produk/' . $produk->id_product, [
            'nama_product' => 'Produk Uji Coba Terupdate',
            'kategori' => 'Elektronik Modern',
            'harga' => 275000,
            'stok' => 50,
            'status_product' => 'tersedia',
            'deskripsi' => 'Deskripsi yang telah diedit.',
        ]);

        $response->assertRedirect('/admin/produk');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('produk', [
            'id_product' => $produk->id_product,
            'nama_product' => 'Produk Uji Coba Terupdate',
            'harga' => 275000,
            'stok' => 50,
        ]);
    }

    public function test_seller_can_delete_product_without_orders(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        
        $produkBaru = Produk::create([
            'penjual_id' => $penjual->id_user,
            'nama_product' => 'Produk Siap Hapus',
            'kategori' => 'Sampel',
            'harga' => 50000,
            'stok' => 5,
            'status_product' => 'tersedia',
            'gambar_product' => 'https://dummyimage.com/100',
        ]);

        $response = $this->actingAs($penjual)->delete('/admin/produk/' . $produkBaru->id_product);
        $response->assertRedirect('/admin/produk');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('produk', [
            'id_product' => $produkBaru->id_product,
        ]);
    }
}
