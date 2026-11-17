<?php
// POST /api/pesanan.php          -> buat pesanan baru (body JSON)
// GET  /api/pesanan.php?id=1     -> lihat detail pesanan
//
// Contoh body POST:
// {
//   "customer_name": "Budi Santoso",
//   "phone": "081234567890",
//   "address": "Jl. Merdeka No. 10, Curup",
//   "buyer_id": 2,                       <- opsional (id user pembeli)
//   "items": [ {"product_id": 1, "qty": 2}, {"product_id": 3, "qty": 1} ]
// }

require_once __DIR__ . '/../koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

// ---------------- GET: detail pesanan ----------------
if ($method === 'GET') {
    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        error_json(400, 'Parameter id wajib diisi dan berupa angka.');
    }
    $id = (int)$_GET['id'];

    $stmt = $koneksi->prepare(
        "SELECT id, buyer_id, customer_name, phone, address, total, status, created_at
         FROM orders WHERE id = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $o = $stmt->get_result()->fetch_assoc();
    if (!$o) {
        error_json(404, "Pesanan dengan id $id tidak ditemukan.");
    }

    $stmt = $koneksi->prepare(
        "SELECT oi.product_id, p.name, oi.qty, oi.price
         FROM order_items oi
         LEFT JOIN products p ON p.id = oi.product_id
         WHERE oi.order_id = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $items = [];
    while ($r = $res->fetch_assoc()) {
        $items[] = [
            'id_produk'    => (int)$r['product_id'],
            'nama_produk'  => $r['name'],
            'jumlah'       => (int)$r['qty'],
            'harga_satuan' => (float)$r['price'],
            'subtotal'     => (float)$r['price'] * (int)$r['qty'],
        ];
    }

    kirim_json('success', ['data' => [
        'id_pesanan' => (int)$o['id'],
        'buyer_id'   => $o['buyer_id'] !== null ? (int)$o['buyer_id'] : null,
        'nama'       => $o['customer_name'],
        'telepon'    => $o['phone'],
        'alamat'     => $o['address'],
        'total'      => (float)$o['total'],
        'status'     => $o['status'],
        'dibuat_pada'=> $o['created_at'],
        'items'      => $items,
    ]]);
}

// ---------------- POST: buat pesanan ----------------
if ($method !== 'POST') {
    error_json(405, 'Method tidak diizinkan. Gunakan GET atau POST.');
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    error_json(400, 'Body request harus berupa JSON yang valid.');
}

$nama     = trim($input['customer_name'] ?? '');
$telepon  = trim($input['phone'] ?? '');
$alamat   = trim($input['address'] ?? '');
$buyer_id = $input['buyer_id'] ?? null;
$items    = $input['items'] ?? null;

if ($nama === '' || $telepon === '' || $alamat === '') {
    error_json(422, 'Field customer_name, phone, dan address wajib diisi.');
}
if (!preg_match('/^\+?[0-9]{8,20}$/', $telepon)) {
    error_json(422, 'Format phone tidak valid (8-20 digit angka).');
}
if ($buyer_id !== null && (!is_int($buyer_id) || $buyer_id < 1)) {
    error_json(422, 'buyer_id harus bilangan bulat positif atau dikosongkan.');
}
if (!is_array($items) || count($items) === 0) {
    error_json(422, 'Field items wajib berisi minimal 1 produk.');
}

$koneksi->begin_transaction();

try {
    // Validasi pembeli (jika dikirim)
    if ($buyer_id !== null) {
        $u = $koneksi->prepare("SELECT id FROM users WHERE id = ?");
        $u->bind_param('i', $buyer_id);
        $u->execute();
        if (!$u->get_result()->fetch_assoc()) {
            throw new Exception("User dengan buyer_id $buyer_id tidak ditemukan.", 404);
        }
    }

    $total  = 0.0;
    $detail = [];
    $cek = $koneksi->prepare("SELECT name, price, stock FROM products WHERE id = ? FOR UPDATE");

    foreach ($items as $i => $it) {
        $pid = $it['product_id'] ?? null;
        $qty = $it['qty'] ?? null;

        if (!is_int($pid) || !is_int($qty) || $pid < 1 || $qty < 1) {
            throw new Exception("Item ke-" . ($i + 1) . ": product_id dan qty harus bilangan bulat positif.", 422);
        }

        $cek->bind_param('i', $pid);
        $cek->execute();
        $p = $cek->get_result()->fetch_assoc();

        if (!$p) {
            throw new Exception("Produk dengan id $pid tidak ditemukan.", 404);
        }
        if ((int)$p['stock'] < $qty) {
            throw new Exception("Stok '{$p['name']}' tidak cukup (tersisa {$p['stock']}).", 409);
        }

        $harga    = (float)$p['price'];
        $total   += $harga * $qty;
        $detail[] = ['pid' => $pid, 'qty' => $qty, 'harga' => $harga];
    }

    $stmt = $koneksi->prepare(
        "INSERT INTO orders (buyer_id, customer_name, phone, address, total, status)
         VALUES (?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->bind_param('isssd', $buyer_id, $nama, $telepon, $alamat, $total);
    if (!$stmt->execute()) {
        throw new Exception('Gagal menyimpan pesanan.', 500);
    }
    $id_pesanan = $koneksi->insert_id;

    $ins = $koneksi->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");
    $upd = $koneksi->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

    foreach ($detail as $d) {
        $ins->bind_param('iiid', $id_pesanan, $d['pid'], $d['qty'], $d['harga']);
        if (!$ins->execute()) {
            throw new Exception('Gagal menyimpan item pesanan.', 500);
        }
        $upd->bind_param('ii', $d['qty'], $d['pid']);
        $upd->execute();
    }

    $koneksi->commit();

    kirim_json('success', [
        'pesan' => 'Pesanan berhasil dibuat.',
        'data'  => ['id_pesanan' => $id_pesanan, 'total' => $total, 'status' => 'pending'],
    ], 201);

} catch (Exception $e) {
    $koneksi->rollback();
    $kode = in_array($e->getCode(), [404, 409, 422, 500], true) ? $e->getCode() : 500;
    error_json($kode, $e->getMessage());
}
