<?php require 'common.php';
if(user()){ header('Location: /index.php'); exit; }
$errs=[]; $v=['name'=>'','email'=>'','role'=>'buyer'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 $v['name']=trim($_POST['name'] ?? ''); $v['email']=trim($_POST['email'] ?? '');
 $v['role']=(($_POST['role'] ?? '')==='seller') ? 'seller' : 'buyer';
 $p=$_POST['password'] ?? ''; $p2=$_POST['password2'] ?? '';
 if(!csrf_ok()) $errs['form']='Sesi tidak valid, muat ulang halaman.';
 if(mb_strlen($v['name'])<3) $errs['name']='Nama minimal 3 karakter.';
 if(!filter_var($v['email'],FILTER_VALIDATE_EMAIL)) $errs['email']='Format email tidak valid.';
 if(strlen($p)<8 || !preg_match('/[A-Za-z]/',$p) || !preg_match('/\d/',$p)) $errs['password']='Password minimal 8 karakter, mengandung huruf dan angka.';
 if($p!==$p2) $errs['password2']='Konfirmasi password tidak sama.';
 if(!$errs){
  $st=$db->prepare("SELECT id FROM users WHERE email=?"); $st->bind_param('s',$v['email']); $st->execute();
  if($st->get_result()->num_rows) $errs['email']='Email sudah terdaftar.';
  else{
   $h=password_hash($p,PASSWORD_DEFAULT);
   $st=$db->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
   $st->bind_param('ssss',$v['name'],$v['email'],$h,$v['role']); $st->execute();
   header('Location: /login.php?registered=1'); exit;
  }
 }
}
function fe($errs,$k){ return isset($errs[$k]) ? '<div class="invalid-feedback d-block">'.e($errs[$k]).'</div>' : ''; }
auth_start('Buat Akun','Daftar sebagai pembeli atau penjual');
if(isset($errs['form'])) echo '<div class="alert alert-danger">'.e($errs['form']).'</div>'; ?>
<form method="post" novalidate>
 <?=csrf_field()?>
 <label class="form-label">Saya ingin</label>
 <div class="row g-2 mb-3">
  <div class="col-6"><input type="radio" class="btn-check" name="role" id="rb" value="buyer" <?=$v['role']==='buyer'?'checked':''?>>
   <label class="btn btn-outline-success w-100 py-3" for="rb"><i class="bi bi-bag fs-4 d-block"></i>Berbelanja</label></div>
  <div class="col-6"><input type="radio" class="btn-check" name="role" id="rs" value="seller" <?=$v['role']==='seller'?'checked':''?>>
   <label class="btn btn-outline-success w-100 py-3" for="rs"><i class="bi bi-shop fs-4 d-block"></i>Berjualan</label></div>
 </div>
 <div class="mb-3"><label class="form-label">Nama lengkap</label>
  <input name="name" class="form-control <?=isset($errs['name'])?'is-invalid':''?>" value="<?=e($v['name'])?>" required><?=fe($errs,'name')?></div>
 <div class="mb-3"><label class="form-label">Email</label>
  <input name="email" type="email" class="form-control <?=isset($errs['email'])?'is-invalid':''?>" value="<?=e($v['email'])?>" required><?=fe($errs,'email')?></div>
 <div class="mb-3"><label class="form-label">Password</label>
  <div class="input-group"><input id="pw" name="password" type="password" class="form-control <?=isset($errs['password'])?'is-invalid':''?>" required>
  <button type="button" class="btn btn-outline-secondary" data-toggle-pw="pw"><i class="bi bi-eye"></i></button></div>
  <div class="progress mt-2" style="height:5px"><div id="bar" class="progress-bar" style="width:0"></div></div>
  <small id="hint" class="text-muted">Minimal 8 karakter, huruf dan angka.</small><?=fe($errs,'password')?></div>
 <div class="mb-4"><label class="form-label">Ulangi password</label>
  <input id="pw2" name="password2" type="password" class="form-control <?=isset($errs['password2'])?'is-invalid':''?>" required><?=fe($errs,'password2')?></div>
 <button class="btn btn-success w-100 py-2">Daftar</button>
</form>
<p class="text-center mt-4 mb-0">Sudah punya akun? <a href="/login.php">Masuk</a></p>
<script>
document.getElementById('pw').addEventListener('input',function(){
 var p=this.value,s=0; if(p.length>=8)s++; if(/[A-Za-z]/.test(p)&&/\d/.test(p))s++; if(p.length>=12||/[^A-Za-z0-9]/.test(p))s++;
 var b=document.getElementById('bar'); b.style.width=(s*33.4)+'%'; b.className='progress-bar '+['bg-danger','bg-danger','bg-warning','bg-success'][s];
 document.getElementById('hint').textContent=['Terlalu lemah','Lemah','Cukup','Kuat'][s];
});
</script>
<?php auth_end();