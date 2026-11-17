<?php
// GET /api/produk.php                  -> semua produk
// GET /api/produk.php?id=1             -> detail produk
// GET /api/produk.php?kategori=2       -> filter berdasarkan kategori
// GET /api/produk.php?q=laptop         -> pencarian nama produk

require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    error_json(405, 'Method tidak diizinkan. Gunakan GET.');
}

$sql_dasar = "SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url,
                     p.created_at, p.category_id, c.name AS category_name
              FROM products p
              LEFT JOIN categories c ON c.id = p.category_id";

function rapikan($r)
{
    return [
        'id'        => (int)$r['id'],
        'nama'      => $r['name'],
        'deskripsi' => $r['description'],
        'harga'     => (float)$r['price'],
        'stok'      => (int)$r['stock'],
        'gambar'    => $r['image_url'],
        'kategori'  => [
            'id'   => $r['category_id'] !== null ? (int)$r['category_id'] : null,
            'nama' => $r['category_name'],
        ],
        'dibuat_pada' => $r['created_at'],
    ];
}

// ---- Detail produk ----
if (isset($_GET['id'])) {
    if (!ctype_digit($_GET['id'])) {
        error_json(400, 'Parameter id harus berupa angka.');
    }
    $id = (int)$_GET['id'];
    $stmt = $koneksi->prepare($sql_dasar . " WHERE p.id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        error_json(404, "Produk dengan id $id tidak ditemukan.");
    }
    kirim_json('success', ['data' => rapikan($row)]);
}

// ---- Daftar produk (opsional filter) ----
$where  = [];
$tipe   = '';
$params = [];

if (isset($_GET['kategori'])) {
    if (!ctype_digit($_GET['kategori'])) {
        error_json(400, 'Parameter kategori harus berupa angka.');
    }
    $where[]  = 'p.category_id = ?';
    $tipe    .= 'i';
    $params[] = (int)$_GET['kategori'];
}
if (isset($_GET['q']) && trim($_GET['q']) !== '') {
    $where[]  = 'p.name LIKE ?';
    $tipe    .= 's';
    $params[] = '%' . trim($_GET['q']) . '%';
}

$sql = $sql_dasar;
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.id ASC';

$stmt = $koneksi->prepare($sql);
if ($params) {
    $stmt->bind_param($tipe, ...$params);
}
$stmt->execute();
$hasil = $stmt->get_result();

$data = [];
while ($row = $hasil->fetch_assoc()) {
    $data[] = rapikan($row);
}

kirim_json('success', ['jumlah' => count($data), 'data' => $data]);
