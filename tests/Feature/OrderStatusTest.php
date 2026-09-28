<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use DatabaseTransactions;
    public function test_seller_can_update_order_status_to_processing(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $order = Order::first();
        $this->assertNotNull($order);

        $response = $this->actingAs($penjual)->put('/admin/orders/' . $order->id_order . '/status', [
            'status_order' => 'diproses',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id_order' => $order->id_order,
            'status_order' => 'diproses',
        ]);
    }

    public function test_seller_can_update_order_status_to_completed(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $order = Order::first();

        $response = $this->actingAs($penjual)->put('/admin/orders/' . $order->id_order . '/status', [
            'status_order' => 'selesai',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id_order' => $order->id_order,
            'status_order' => 'selesai',
        ]);
    }

    public function test_cancelling_order_restores_product_stock(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $order = Order::with('produk')->where('status_order', '!=', 'dibatalkan')->first();
        $this->assertNotNull($order);

        $initialStock = $order->produk->stok;

        $response = $this->actingAs($penjual)->put('/admin/orders/' . $order->id_order . '/status', [
            'status_order' => 'dibatalkan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan status order menjadi dibatalkan
        $this->assertDatabaseHas('orders', [
            'id_order' => $order->id_order,
            'status_order' => 'dibatalkan',
        ]);

        // Pastikan stok dikembalikan (+1)
        $order->produk->refresh();
        $this->assertEquals($initialStock + 1, $order->produk->stok);
    }
}
