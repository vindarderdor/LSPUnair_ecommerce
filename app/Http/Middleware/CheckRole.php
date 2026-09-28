<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Cek status keaktifan user
        if ($user->status_user !== 'aktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda berstatus tidak aktif. Silakan hubungi admin.');
        }

        // Cek role user
        if ($user->role !== $role) {
            if ($role === 'penjual') {
                return redirect()->route('shop.index')->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengakses Dashboard Admin.');
            }
            return redirect()->route('admin.dashboard')->with('error', 'Halaman ini khusus untuk pembeli.');
        }

        return $next($request);
    }
}
