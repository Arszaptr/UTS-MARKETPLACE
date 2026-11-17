-- ============================================================
-- Database Marketplace (replika struktur Soal 11) untuk pengujian REST API
-- Import lewat phpMyAdmin (XAMPP) atau:  mysql -u root < marketplace.sql
--
-- CATATAN: struktur tabel categories, products, orders, order_items
-- mengikuti database asli. Kolom tabel users (selain id) adalah perkiraan;
-- jika Anda mengekspor database asli dari phpMyAdmin, gunakan file ekspor tsb.
-- ============================================================

CREATE DATABASE IF NOT EXISTS marketplaceee
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE marketplaceee;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id          INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  email       VARCHAR(100) NOT NULL,
  password    VARCHAR(255) NOT NULL,
  role        VARCHAR(20) NOT NULL DEFAULT 'buyer',
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id    INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE products (
  id           INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_id  INT(11) NOT NULL,
  seller_id    INT(11) NOT NULL,
  name         VARCHAR(150) NOT NULL,
  description  TEXT NULL,
  price        DECIMAL(12,2) NOT NULL,
  stock        INT(11) NOT NULL,
  image_url    VARCHAR(255) NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY (category_id),
  KEY (seller_id),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (seller_id)   REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE orders (
  id             INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  buyer_id       INT(11) NULL,
  customer_name  VARCHAR(100) NOT NULL,
  phone          VARCHAR(20) NOT NULL,
  address        TEXT NOT NULL,
  total          DECIMAL(12,2) NOT NULL,
  status         ENUM('pending','paid','shipped','done') NULL DEFAULT 'pending',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY (buyer_id),
  FOREIGN KEY (buyer_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id          INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  order_id    INT(11) NOT NULL,
  product_id  INT(11) NOT NULL,
  qty         INT(11) NOT NULL,
  price       DECIMAL(12,2) NOT NULL,
  KEY (order_id),
  KEY (product_id),
  FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- ---------------- Data uji ----------------
INSERT INTO users (name, email, password, role) VALUES
('Toko Contoh',  'seller@example.com', 'HASH_CONTOH_BUKAN_PASSWORD_ASLI', 'seller'),
('Budi Santoso', 'budi@example.com',   'HASH_CONTOH_BUKAN_PASSWORD_ASLI', 'buyer');

INSERT INTO categories (name) VALUES
('Elektronik'), ('Fashion'), ('Makanan & Minuman'), ('Buku & Alat Tulis');

INSERT INTO products (category_id, seller_id, name, description, price, stock, image_url) VALUES
(1, 1, 'Headset Bluetooth X1',   'Headset nirkabel dengan baterai 20 jam.',            185000.00, 25,  'headset.jpg'),
(1, 1, 'Power Bank 10000 mAh',   'Power bank slim dengan fast charging.',              150000.00, 40,  'powerbank.jpg'),
(2, 1, 'Kaos Polos Cotton',      'Kaos katun combed 30s, tersedia ukuran S-XL.',        65000.00, 100, 'kaos.jpg'),
(2, 1, 'Tas Ransel Kampus',      'Ransel anti air dengan slot laptop 15 inci.',        225000.00, 15,  'ransel.jpg'),
(3, 1, 'Kopi Kerinci 250 gr',    'Biji kopi arabika asli Kerinci, roasting medium.',    75000.00, 60,  'kopi.jpg'),
(4, 1, 'Buku Tulis Sidu 58 Lbr', 'Buku tulis isi 58 lembar, 1 pak isi 10.',             42000.00, 80,  'buku.jpg');
