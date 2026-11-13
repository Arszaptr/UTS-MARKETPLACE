<?php
date_default_timezone_set('Asia/Jakarta');
require __DIR__.'/config.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) die('Koneksi database gagal.');
$db->set_charset('utf8mb4');
$db->query("SET time_zone = '+07:00'");