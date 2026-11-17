<?php
// GET /api/kategori.php        -> daftar kategori (beserta jumlah produk)
// GET /api/kategori.php?id=1   -> detail satu kategori

require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    error_json(405, 'Method tidak diizinkan. Gunakan GET.');
}

$sql = "SELECT c.id, c.name, COUNT(p.id) AS jumlah_produk
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id";

function rapikan_kategori($r)
{
    return [
        'id'            => (int)$r['id'],
        'nama'          => $r['name'],
        'jumlah_produk' => (int)$r['jumlah_produk'],
    ];
}

if (isset($_GET['id'])) {
    if (!ctype_digit($_GET['id'])) {
        error_json(400, 'Parameter id harus berupa angka.');
    }
    $id = (int)$_GET['id'];
    $stmt = $koneksi->prepare($sql . " WHERE c.id = ? GROUP BY c.id, c.name");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        error_json(404, "Kategori dengan id $id tidak ditemukan.");
    }
    kirim_json('success', ['data' => rapikan_kategori($row)]);
}

$hasil = $koneksi->query($sql . " GROUP BY c.id, c.name ORDER BY c.id ASC");
$data = [];
while ($row = $hasil->fetch_assoc()) {
    $data[] = rapikan_kategori($row);
}

kirim_json('success', ['jumlah' => count($data), 'data' => $data]);
