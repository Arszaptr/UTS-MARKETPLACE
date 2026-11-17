# REST API Marketplace (PHP Native + MySQLi)

UTS Soal 12 — Pengembangan Web Service / REST API Marketplace.
Database: `marketplaceee` (tabel `categories`, `products`, `orders`, `order_items`, `users`).

## Struktur Folder

```
marketplace-api/
├── api/
│   ├── produk.php      # GET daftar produk / detail produk
│   ├── kategori.php    # GET daftar kategori
│   └── pesanan.php     # POST buat pesanan, GET detail pesanan
├── database/
│   └── marketplace.sql # struktur + data uji
├── koneksi.php         # koneksi MySQLi + helper response JSON
├── config.example.php  # contoh konfigurasi (config.php di-ignore Git)
└── .gitignore
```

## Cara Menjalankan (XAMPP)

1. Jalankan **Apache** dan **MySQL** di XAMPP Control Panel.
2. Salin folder `marketplace-api` ke `C:\xampp\htdocs\`.
3. Buka `http://localhost/phpmyadmin` → **Import** → pilih `database/marketplace.sql`
   (atau gunakan hasil ekspor database asli dari InfinityFree).
4. Salin `config.example.php` menjadi `config.php`, pastikan `'db' => 'marketplaceee'`.
5. Buka `http://localhost/marketplace-api/api/produk.php` di browser.

---

## A. Analisis dan Perancangan API

### a. Fungsi Web Service/API pada Marketplace

Web Service/API adalah antarmuka yang memungkinkan satu aplikasi meminta dan mengirim data ke aplikasi
lain melalui protokol HTTP dengan format standar (JSON), tanpa mengakses database secara langsung.

API diperlukan pada marketplace karena:

- **Pemisahan data dan tampilan** — satu sumber data dipakai website, aplikasi mobile, dan pihak ketiga.
- **Interoperabilitas** — aplikasi beda bahasa/platform (Android, iOS, JavaScript) tetap bisa berkomunikasi lewat HTTP + JSON.
- **Keamanan** — database tidak dibuka ke luar; akses hanya lewat endpoint yang divalidasi server.
- **Integrasi dan skalabilitas** — mitra (kurir, kasir, agregator produk) dapat terhubung tanpa mengubah kode inti.
- **Efisiensi** — logika bisnis (cek stok, hitung total) cukup ditulis sekali di server.

### b. Rancangan Endpoint

| No | Method | Endpoint | Fungsi | Sukses | Gagal |
|----|--------|----------|--------|--------|-------|
| 1 | GET | `/api/produk.php` | Seluruh produk (opsional `?kategori=`, `?q=`) | 200 | 400, 405 |
| 2 | GET | `/api/produk.php?id=1` | Detail produk | 200 | 400, 404 |
| 3 | GET | `/api/kategori.php` | Daftar kategori | 200 | 405 |
| 4 | POST | `/api/pesanan.php` | Membuat pesanan baru | 201 | 400, 404, 409, 422 |
| 5 | GET | `/api/pesanan.php?id=1` | Detail pesanan | 200 | 400, 404 |

### c. Contoh Struktur JSON

**Daftar produk** — `GET /api/produk.php`

```json
{
    "status": "success",
    "jumlah": 2,
    "data": [
        {
            "id": 1,
            "nama": "Headset Bluetooth X1",
            "deskripsi": "Headset nirkabel dengan baterai 20 jam.",
            "harga": 185000,
            "stok": 25,
            "gambar": "headset.jpg",
            "kategori": { "id": 1, "nama": "Elektronik" },
            "dibuat_pada": "2026-11-17 12:00:00"
        },
        {
            "id": 2,
            "nama": "Power Bank 10000 mAh",
            "deskripsi": "Power bank slim dengan fast charging.",
            "harga": 150000,
            "stok": 40,
            "gambar": "powerbank.jpg",
            "kategori": { "id": 1, "nama": "Elektronik" },
            "dibuat_pada": "2026-11-17 12:00:00"
        }
    ]
}
```

**Detail produk** — `GET /api/produk.php?id=1`

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "nama": "Headset Bluetooth X1",
        "deskripsi": "Headset nirkabel dengan baterai 20 jam.",
        "harga": 185000,
        "stok": 25,
        "gambar": "headset.jpg",
        "kategori": { "id": 1, "nama": "Elektronik" },
        "dibuat_pada": "2026-11-17 12:00:00"
    }
}
```

**Produk tidak ditemukan** — `GET /api/produk.php?id=999` (HTTP 404)

```json
{
    "status": "error",
    "pesan": "Produk dengan id 999 tidak ditemukan."
}
```

> Catatan: contoh di atas hanya untuk perancangan. Untuk laporan pengujian, gunakan
> response asli dari XAMPP Anda.

---

## B. Panduan Pengujian

### 1. Browser

| Tujuan | URL |
|--------|-----|
| Semua produk | `http://localhost/marketplace-api/api/produk.php` |
| Detail produk | `http://localhost/marketplace-api/api/produk.php?id=1` |
| Produk tidak ada | `http://localhost/marketplace-api/api/produk.php?id=999` |
| Daftar kategori | `http://localhost/marketplace-api/api/kategori.php` |

### 2. Postman

- **GET semua produk**: `GET http://localhost/marketplace-api/api/produk.php` → Send.
- **GET detail produk**: `GET http://localhost/marketplace-api/api/produk.php?id=1` → Send.
- **POST pesanan**: `POST http://localhost/marketplace-api/api/pesanan.php`,
  tab **Body → raw → JSON**:

```json
{
    "customer_name": "Budi Santoso",
    "phone": "081234567890",
    "address": "Jl. Merdeka No. 10, Curup",
    "buyer_id": 2,
    "items": [
        { "product_id": 1, "qty": 2 },
        { "product_id": 3, "qty": 1 }
    ]
}
```

Respons yang diharapkan (HTTP 201), dengan data uji dari `marketplace.sql`
(2 × 185.000 + 1 × 65.000 = 435.000):

```json
{
    "status": "success",
    "pesan": "Pesanan berhasil dibuat.",
    "data": { "id_pesanan": 1, "total": 435000, "status": "pending" }
}
```

`buyer_id` bersifat opsional. Jika dikirim, harus ada di tabel `users`
(pada data uji: id 2). Sesuaikan jika data Anda berbeda.

**Skenario error yang perlu diuji:**

| Skenario | Hasil |
|----------|-------|
| `GET produk.php?id=999` | 404 |
| `GET produk.php?id=abc` | 400 |
| `POST pesanan.php` dengan body bukan JSON | 400 |
| `POST pesanan.php` tanpa field `phone` | 422 |
| `POST pesanan.php` dengan `qty` melebihi stok | 409 |
| `POST pesanan.php` dengan `product_id` yang tidak ada | 404 |
| `DELETE produk.php` | 405 |

### 3. Verifikasi di Database

Setelah POST berhasil, jalankan di phpMyAdmin:

```sql
SELECT * FROM orders;
SELECT * FROM order_items;
SELECT id, name, stock FROM products;  -- stok harus berkurang
```

---

## C. Daftar Screenshot untuk Laporan

1. XAMPP Control Panel — Apache dan MySQL *Running*.
2. phpMyAdmin — database `marketplaceee` dan tabel-tabelnya.
3. Browser — `produk.php` (URL `localhost` terlihat + JSON).
4. Browser — `produk.php?id=1`.
5. Browser — `kategori.php`.
6. Browser — `produk.php?id=999` (respons 404).
7. Postman — GET semua produk (method, URL, status 200, body).
8. Postman — GET detail produk.
9. Postman — POST pesanan (tab Body terlihat, status 201).
10. Postman — POST gagal (stok tidak cukup / validasi, 409/422).
11. phpMyAdmin — tabel `orders` dan `order_items` terisi, stok `products` berkurang.
12. GitHub — halaman repository.

## D. Upload ke GitHub

```bash
cd marketplace-api
git init
git add .
git commit -m "REST API marketplace UTS Soal 12"
git branch -M main
git remote add origin https://github.com/USERNAME/marketplace-api.git
git push -u origin main
```

`config.php` sudah masuk `.gitignore` sehingga password database tidak ikut terunggah.

**URL Repository:** `https://github.com/USERNAME/marketplace-api` *(ganti dengan milik Anda)*
