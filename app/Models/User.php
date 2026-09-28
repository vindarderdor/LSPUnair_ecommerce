<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel di database
     */
    protected $table = 'users';

    /**
     * Primary key kustom
     */
    protected $primaryKey = 'id_user';

    /**
     * Tabel tidak menggunakan created_at & updated_at bawaan Laravel
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi secara massal
     */
    protected $fillable = [
        'nama_user',
        'email',
        'password',
        'role',
        'tgl_daftar',
        'foto_profil',
        'status_user',
        'remember_token',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'tgl_daftar' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Cek apakah role adalah penjual
     */
    public function isPenjual(): bool
    {
        return $this->role === 'penjual';
    }

    /**
     * Cek apakah role adalah pembeli
     */
    public function isPembeli(): bool
    {
        return $this->role === 'pembeli';
    }

    /**
     * Cek apakah status user aktif
     */
    public function isAktif(): bool
    {
        return $this->status_user === 'aktif';
    }
}
