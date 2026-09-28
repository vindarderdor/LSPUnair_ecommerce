<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_from_admin_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_pembeli_cannot_access_admin_dashboard(): void
    {
        $pembeli = User::where('email', 'pembeli@gmail.com')->first();
        $response = $this->actingAs($pembeli)->get('/admin');
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_penjual_can_access_admin_dashboard(): void
    {
        $penjual = User::where('email', 'admin@toko.com')->first();
        $response = $this->actingAs($penjual)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Dashboard');
    }

    public function test_login_success_for_penjual(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@toko.com',
            'password' => 'admin123',
        ]);
        $response->assertRedirect('/admin');
        $this->assertAuthenticated();
    }

    public function test_login_success_for_pembeli(): void
    {
        $response = $this->post('/login', [
            'email' => 'pembeli@gmail.com',
            'password' => 'pembeli123',
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_login_success_for_devinno(): void
    {
        $devinno = User::where('email', 'dev@gmail.com')->first();
        $this->assertNotNull($devinno);
        $this->assertEquals('devinno', $devinno->nama_user);
        $this->assertEquals('pembeli', $devinno->role);

        $response = $this->actingAs($devinno)->get('/pesanan-saya');
        $response->assertStatus(200);
    }

    public function test_register_page_is_accessible_and_validates_existing_email(): void
    {
        // Akses halaman register
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');

        // Uji validasi menggunakan email yang sudah ada tanpa membuat user baru
        $responsePost = $this->post('/register', [
            'nama_user' => 'Duplikat User',
            'email' => 'pembeli@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $responsePost->assertSessionHasErrors('email');
    }

    public function test_user_can_logout_successfully(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        // Test POST logout
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $response->assertSessionHas('success');
        $this->assertGuest();

        // Test GET logout
        $responseGet = $this->actingAs($user)->get('/logout');
        $responseGet->assertRedirect('/login');
        $responseGet->assertSessionHas('success');
        $this->assertGuest();
    }

    public function test_seller_can_update_user_role(): void
    {
        $penjual = User::where('role', 'penjual')->first();
        $pembeli = User::where('role', 'pembeli')->first();
        $this->assertNotNull($penjual);
        $this->assertNotNull($pembeli);

        $response = $this->actingAs($penjual)->put('/admin/users/' . $pembeli->id_user . '/role', [
            'role' => 'penjual',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id_user' => $pembeli->id_user,
            'role' => 'penjual',
        ]);
    }
}
