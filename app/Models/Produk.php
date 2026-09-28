<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'produk';

    /**
     * Primary key kustom
     */
    protected $primaryKey = 'id_product';

    /**
     * Tabel tidak menggunakan timestamps bawaan
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi
     */
    protected $fillable = [
        'penjual_id',
        'nama_product',
        'deskripsi',
        'harga',
        'stok',
        'kategori',
        'gambar_product',
        'status_product',
    ];

    /**
     * Format harga ke Rupiah
     */
    public function getHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Relasi ke penjual (User)
     */
    public function penjual()
    {
        return $this->belongsTo(User::class, 'penjual_id', 'id_user');
    }

    /**
     * Relasi ke pesanan (Orders)
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_product', 'id_product');
    }
}
