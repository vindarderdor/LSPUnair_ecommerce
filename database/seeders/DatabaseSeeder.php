<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data pengguna, produk, dan pesanan awal
     */
    public function run(): void
    {
        // 1. Akun Penjual / Admin Toko
        $penjual = User::updateOrCreate(
            ['email' => 'admin@toko.com'],
            [
                'nama_user' => 'Admin Penjual',
                'password' => Hash::make('admin123'),
                'role' => 'penjual',
                'tgl_daftar' => now()->subMonths(2)->toDateString(),
                'status_user' => 'aktif',
            ]
        );

        // 2. Akun Pembeli / Customer
        $pembeli = User::updateOrCreate(
            ['email' => 'pembeli@gmail.com'],
            [
                'nama_user' => 'Budi Pembeli',
                'password' => Hash::make('pembeli123'),
                'role' => 'pembeli',
                'tgl_daftar' => now()->subDays(15)->toDateString(),
                'status_user' => 'aktif',
            ]
        );

        // 3. Akun Pembeli ke-2: devinno
        $devinno = User::updateOrCreate(
            ['email' => 'dev@gmail.com'],
            [
                'nama_user' => 'devinno',
                'password' => Hash::make('password123'),
                'role' => 'pembeli',
                'tgl_daftar' => now()->toDateString(),
                'status_user' => 'aktif',
            ]
        );

        // 3. Data Produk Dummy Realistis
        $daftarProduk = [
            [
                'nama_product' => 'Sepatu Sneakers Kasual Sporty',
                'deskripsi' => 'Sneakers berkualitas tinggi dengan bahan breathable mesh dan sol karet anti slip, nyaman dipakai seharian.',
                'harga' => 350000,
                'stok' => 15,
                'kategori' => 'Fashion Pria',
                'gambar_product' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'nama_product' => 'Jam Tangan Elegan Chronograph',
                'deskripsi' => 'Jam tangan pria dengan strap kulit asli, tahan air hingga 30m, dilengkapi fitur penunjuk tanggal otomatis.',
                'harga' => 480000,
                'stok' => 8,
                'kategori' => 'Aksesoris',
                'gambar_product' => 'https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'nama_product' => 'Kemeja Oxford Kasual Lengan Panjang',
                'deskripsi' => 'Bahan katun oxford premium halus dan adem, potongan slim fit modern cocok untuk kerja maupun hangout.',
                'harga' => 195000,
                'stok' => 20,
                'kategori' => 'Fashion Pria',
                'gambar_product' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'nama_product' => 'Tas Ransel Laptop Anti Air (Waterproof)',
                'deskripsi' => 'Tas ransel berkapasitas 25L dengan slot laptop hingga 15.6 inch, port USB eksternal, dan busa punggung tebal.',
                'harga' => 275000,
                'stok' => 12,
                'kategori' => 'Tas & Aksesoris',
                'gambar_product' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'nama_product' => 'Headphone Wireless Bluetooth 5.3',
                'deskripsi' => 'Suara bass bertenaga dengan teknologi Active Noise Cancelling (ANC), daya tahan baterai hingga 40 jam penggunaan.',
                'harga' => 420000,
                'stok' => 10,
                'kategori' => 'Elektronik',
                'gambar_product' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'nama_product' => 'Tumbler Stainless Vacuum Insulated 500ml',
                'deskripsi' => 'Mampu menjaga suhu panas hingga 12 jam dan dingin hingga 24 jam. Bebas BPA dan anti tumpah.',
                'harga' => 95000,
                'stok' => 35,
                'kategori' => 'Perlengkapan Rumah',
                'gambar_product' => 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'nama_product' => 'Jaket Hoodie Fleece Tebal Unisex',
                'deskripsi' => 'Bahan cotton fleece tebal dan lembut, jahitan rapi kuat, hangat dipakai untuk berkendara atau cuaca dingin.',
                'harga' => 220000,
                'stok' => 18,
                'kategori' => 'Fashion Pria',
                'gambar_product' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'nama_product' => 'Dompet Kulit Bifold Slim Klasik',
                'deskripsi' => 'Dompet pria berbahan kulit sintetis premium dengan 8 slot kartu, 2 slot uang kertas, dan teknologi anti-RFID.',
                'harga' => 150000,
                'stok' => 14,
                'kategori' => 'Aksesoris',
                'gambar_product' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
        ];

        foreach ($daftarProduk as $item) {
            Produk::updateOrCreate(
                ['nama_product' => $item['nama_product']],
                array_merge($item, ['penjual_id' => $penjual->id_user])
            );
        }

        // 4. Data Pesanan Dummy Realistis untuk Tabel Admin
        $produkList = Produk::all();
        if ($produkList->count() >= 4) {
            Order::updateOrCreate(
                [
                    'pembeli_id' => $pembeli->id_user,
                    'id_product' => $produkList[0]->id_product,
                ],
                [
                    'tgl_order' => now()->subHours(2),
                    'total_harga' => $produkList[0]->harga,
                    'status_order' => 'menunggu_konfirmasi',
                    'alamat_pengiriman' => 'Jl. Pemuda No. 45, Surabaya, Jawa Timur',
                    'bukti_bayar' => 'bukti_transfer_1.jpg',
                ]
            );

            Order::updateOrCreate(
                [
                    'pembeli_id' => $devinno->id_user,
                    'id_product' => $produkList[1]->id_product,
                ],
                [
                    'tgl_order' => now()->subDay(),
                    'total_harga' => $produkList[1]->harga,
                    'status_order' => 'diproses',
                    'alamat_pengiriman' => 'Jl. Malioboro No. 12, Yogyakarta',
                    'bukti_bayar' => 'bukti_transfer_2.jpg',
                ]
            );

            Order::updateOrCreate(
                [
                    'pembeli_id' => $pembeli->id_user,
                    'id_product' => $produkList[2]->id_product,
                ],
                [
                    'tgl_order' => now()->subDays(2),
                    'total_harga' => $produkList[2]->harga,
                    'status_order' => 'dikirim',
                    'alamat_pengiriman' => 'Jl. Sudirman No. 88, Jakarta Selatan',
                    'bukti_bayar' => 'bukti_transfer_3.jpg',
                ]
            );

            Order::updateOrCreate(
                [
                    'pembeli_id' => $devinno->id_user,
                    'id_product' => $produkList[3]->id_product,
                ],
                [
                    'tgl_order' => now()->subDays(4),
                    'total_harga' => $produkList[3]->harga,
                    'status_order' => 'selesai',
                    'alamat_pengiriman' => 'Jl. Diponegoro No. 17, Bandung, Jawa Barat',
                    'bukti_bayar' => 'bukti_transfer_4.jpg',
                ]
            );
        }
    }
}
