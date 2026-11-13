<?php require 'common.php';
if(!function_exists('go')){
 function go($url){
  if(!headers_sent()){ header('Location: '.$url); exit; }
  echo '<script>location.href='.json_encode($url).'</script><noscript><a href="'.e($url).'">Lanjutkan</a></noscript>'; exit;
 }
}
if(user()){ go('/index.php'); }
$err=''; $email=''; $next=safe_next($_GET['next'] ?? $_POST['next'] ?? '');
$locked = ($_SESSION['lock_until'] ?? 0) > time();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $email=trim($_POST['email'] ?? '');
 if(!csrf_ok()) $err='Sesi tidak valid, muat ulang halaman.';
 elseif($locked) $err='Terlalu banyak percobaan. Coba lagi dalam '.($_SESSION['lock_until']-time()).' detik.';
 else{
  $st=$db->prepare("SELECT * FROM users WHERE email=?"); $st->bind_param('s',$email); $st->execute();
  $u=$st->get_result()->fetch_assoc();
  if($u && password_verify($_POST['password'] ?? '',$u['password'])){
   session_regenerate_id(true);
   $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']];
   unset($_SESSION['fails'],$_SESSION['lock_until']);
   $dest = $next ?: (['admin'=>'/panel/index.php','seller'=>'/admin/products.php'][$u['role']] ?? '/index.php');
   go($dest);
  }
  $_SESSION['fails']=($_SESSION['fails'] ?? 0)+1;
  if($_SESSION['fails']>=5){ $_SESSION['lock_until']=time()+60; $_SESSION['fails']=0; $err='Terlalu banyak percobaan. Coba lagi dalam 60 detik.'; }
  else $err='Email atau password salah.';
 }
}
auth_start('Masuk','Masuk untuk mulai berbelanja atau berjualan');
if(isset($_GET['registered'])) echo '<div class="alert alert-success"><i class="bi bi-check-circle"></i> Pendaftaran berhasil, silakan masuk.</div>';
if(isset($_GET['need'])) echo '<div class="alert alert-info">Silakan masuk untuk melanjutkan.</div>';
if($err) echo '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> '.e($err).'</div>';
?>
<form method="post" novalidate>
 <?=csrf_field()?><input type="hidden" name="next" value="<?=e($next)?>">
 <div class="mb-3"><label class="form-label">Email</label>
  <div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span>
  <input name="email" type="email" class="form-control" value="<?=e($email)?>" placeholder="nama@email.com" required autofocus></div></div>
 <div class="mb-4"><label class="form-label">Password</label>
  <div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span>
  <input id="pw" name="password" type="password" class="form-control" placeholder="Password" required>
  <button type="button" class="btn btn-outline-secondary" data-toggle-pw="pw"><i class="bi bi-eye"></i></button></div></div>
 <button class="btn btn-success w-100 py-2" <?=$locked?'disabled':''?>>Masuk</button>
</form>
<p class="text-center mt-4 mb-0">Belum punya akun? <a href="/register.php<?=$next?'?next='.urlencode($next):''?>">Daftar sekarang</a></p>
<?php auth_end();