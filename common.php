<?php
session_start();
require __DIR__.'/koneksi.php';
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n){ return 'Rp '.number_format($n,0,',','.'); }
function user(){ return $_SESSION['user'] ?? null; }
function require_role($role){
 $u=user();
 if(!$u){ header('Location: /login.php?next='.urlencode($_SERVER['REQUEST_URI'])); exit; }
 if($u['role']!==$role){ http_response_code(403); header_html('Akses ditolak'); echo '<div class="alert alert-danger">Halaman ini khusus '.e($role).'.</div>'; footer_html(); exit; }
}
function header_html($title='Marketplace'){
 $n=array_sum($_SESSION['cart'] ?? []); $u=user();
 echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).'</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"></head><body>
 <nav class="navbar navbar-dark bg-success mb-4"><div class="container"><a class="navbar-brand" href="/index.php">Pasar Online</a><div class="d-flex gap-1 flex-wrap">';
 if(!$u || $u['role']==='buyer') echo '<a class="btn btn-light btn-sm" href="/cart.php">Keranjang ('.$n.')</a>';
 if($u && $u['role']==='buyer') echo '<a class="btn btn-outline-light btn-sm" href="/orders.php">Pesanan Saya</a>';
 if($u && $u['role']==='admin') echo '<a class="btn btn-warning btn-sm" href="/panel/index.php">Panel Admin</a>';
 if($u && $u['role']==='seller') echo '<a class="btn btn-outline-light btn-sm" href="/admin/products.php">Produk Saya</a><a class="btn btn-outline-light btn-sm" href="/admin/orders.php">Pesanan Masuk</a>';
 if($u) echo '<span class="navbar-text mx-2">'.e($u['name']).' ('.e($u['role']).')</span><form method="post" action="/logout.php" class="d-inline">'.csrf_field().'<button class="btn btn-danger btn-sm">Logout</button></form>';
 else echo '<a class="btn btn-outline-light btn-sm" href="/login.php">Login</a><a class="btn btn-warning btn-sm" href="/register.php">Daftar</a>';
 echo '</div></div></nav><div class="container">';
}
function footer_html(){ echo '</div></body></html>'; }

// ---- Keamanan & helper autentikasi ----
function csrf_token(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_field(){ return '<input type="hidden" name="csrf" value="'.csrf_token().'">'; }
function csrf_ok(){ return isset($_POST['csrf'],$_SESSION['csrf']) && hash_equals($_SESSION['csrf'],$_POST['csrf']); }
function safe_next($n){ return (is_string($n) && $n!=='' && $n[0]==='/' && substr($n,0,2)!=='//' && strpos($n,'\\')===false) ? $n : ''; }
function auth_start($title,$sub){
 header_html($title);
 echo '<div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card shadow border-0"><div class="card-body p-4 p-md-5">
 <div class="text-center mb-4"><i class="bi bi-shop fs-1 text-success"></i><h3 class="mt-2 mb-1">'.e($title).'</h3><p class="text-muted mb-0">'.e($sub).'</p></div>';
}
function auth_end(){
 echo '</div></div></div></div><script>
 document.querySelectorAll("[data-toggle-pw]").forEach(function(b){b.addEventListener("click",function(){var i=document.getElementById(b.dataset.togglePw);var s=i.type==="password";i.type=s?"text":"password";b.innerHTML=s?"<i class=\"bi bi-eye-slash\"></i>":"<i class=\"bi bi-eye\"></i>";});});
 </script>';
 footer_html();
}
function go($url){
 if(!headers_sent()){ header('Location: '.$url); exit; }
 echo '<script>location.href='.json_encode($url).'</script><noscript><a href="'.e($url).'">Lanjutkan</a></noscript>'; exit;
}
function tgl($s){
 if(!$s) return '-';
 $b=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
 $t=strtotime($s);
 return date('d',$t).' '.$b[(int)date('n',$t)-1].' '.date('Y, H:i',$t).' WIB';
}