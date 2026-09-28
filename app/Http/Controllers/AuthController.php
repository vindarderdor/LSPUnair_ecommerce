<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman form login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isPenjual()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('shop.index');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput()->with('error', 'Email atau password yang Anda masukkan salah.');
        }

        if ($user->status_user !== 'aktif') {
            return back()->withInput()->with('error', 'Akun Anda berstatus tidak aktif. Silakan hubungi admin.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->isPenjual()) {
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang di Dashboard Admin, ' . $user->nama_user . '!');
        }

        return redirect()->intended(route('shop.index'))->with('success', 'Selamat berbelanja, ' . $user->nama_user . '!');
    }

    /**
     * Tampilkan halaman form registrasi
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isPenjual()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('shop.index');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'nama_user.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'nama_user' => $request->nama_user,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
            'tgl_daftar' => now()->toDateString(),
            'status_user' => 'aktif',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('shop.index')->with('success', 'Pendaftaran berhasil! Selamat datang, ' . $user->nama_user . '!');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar (logout).');
    }
}
