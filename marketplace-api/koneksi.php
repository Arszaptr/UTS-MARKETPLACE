<?php
// Koneksi database (MySQLi) + fungsi bantu response JSON.

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

mysqli_report(MYSQLI_REPORT_OFF);

function kirim_json($status, $data, $kode = 200)
{
    http_response_code($kode);
    echo json_encode(
        array_merge(['status' => $status], $data),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
    );
    exit;
}

function error_json($kode, $pesan)
{
    kirim_json('error', ['pesan' => $pesan], $kode);
}

// Preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$file_config = __DIR__ . '/config.php';
if (!file_exists($file_config)) {
    error_json(500, 'File config.php belum dibuat. Salin dari config.example.php.');
}
$cfg = require $file_config;

$koneksi = @new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db']);
if ($koneksi->connect_errno) {
    error_json(500, 'Koneksi database gagal.');
}
$koneksi->set_charset('utf8mb4');
