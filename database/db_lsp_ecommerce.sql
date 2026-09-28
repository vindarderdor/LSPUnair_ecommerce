-- Database: db_lsp_ecommerce
CREATE DATABASE IF NOT EXISTS db_lsp_ecommerce;
USE db_lsp_ecommerce;

-- 1. Table: users
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_user VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('pembeli', 'penjual') NOT NULL,
    tgl_daftar DATE NOT NULL,
    foto_profil VARCHAR(255),
    status_user ENUM('aktif', 'tidak_aktif') NOT NULL DEFAULT 'aktif',
    remember_token VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table: produk
CREATE TABLE IF NOT EXISTS produk (
    id_product INT AUTO_INCREMENT PRIMARY KEY,
    penjual_id INT NOT NULL,
    nama_product VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(15,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    kategori VARCHAR(100),
    gambar_product VARCHAR(255),
    status_product ENUM('tersedia', 'habis') NOT NULL DEFAULT 'tersedia',

    CONSTRAINT fk_produk_penjual
        FOREIGN KEY (penjual_id)
        REFERENCES users(id_user)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table: orders
CREATE TABLE IF NOT EXISTS orders (
    id_order INT AUTO_INCREMENT PRIMARY KEY,
    pembeli_id INT NOT NULL,
    id_product INT NOT NULL,
    tgl_order DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total_harga DECIMAL(15,2) NOT NULL,
    status_order ENUM(
        'menunggu_bayar',
        'menunggu_konfirmasi',
        'diproses',
        'dikirim',
        'selesai',
        'dibatalkan'
    ) NOT NULL DEFAULT 'menunggu_bayar',
    alamat_pengiriman TEXT NOT NULL,
    bukti_bayar VARCHAR(255),

    CONSTRAINT fk_order_pembeli
        FOREIGN KEY (pembeli_id)
        REFERENCES users(id_user)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_order_produk
        FOREIGN KEY (id_product)
        REFERENCES produk(id_product)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
