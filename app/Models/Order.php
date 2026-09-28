<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Nama tabel
     */
    protected $table = 'orders';

    /**
     * Primary key kustom
     */
    protected $primaryKey = 'id_order';

    /**
     * Tabel tidak menggunakan timestamps bawaan
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi
     */
    protected $fillable = [
        'pembeli_id',
        'id_product',
        'tgl_order',
        'total_harga',
        'status_order',
        'alamat_pengiriman',
        'bukti_bayar',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'tgl_order' => 'datetime',
    ];

    /**
     * Format total harga ke Rupiah
     */
    public function getTotalHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    /**
     * Relasi ke pembeli (User)
     */
    public function pembeli()
    {
        return $this->belongsTo(User::class, 'pembeli_id', 'id_user');
    }

    /**
     * Relasi ke produk (Produk)
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_product', 'id_product');
    }

    /**
     * Badge status order untuk UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_order) {
            'menunggu_bayar' => '<span class="badge bg-secondary badge-secondary">Menunggu Bayar</span>',
            'menunggu_konfirmasi' => '<span class="badge bg-warning badge-warning text-dark">Menunggu Konfirmasi</span>',
            'diproses' => '<span class="badge bg-info badge-info">Diproses</span>',
            'dikirim' => '<span class="badge bg-primary badge-primary">Dikirim</span>',
            'selesai' => '<span class="badge bg-success badge-success">Selesai</span>',
            'dibatalkan' => '<span class="badge bg-danger badge-danger">Dibatalkan</span>',
            default => '<span class="badge bg-light badge-light">' . $this->status_order . '</span>',
        };
    }
}
