<?php require __DIR__.'/../common.php'; require __DIR__.'/_nav.php'; require_role('admin'); $me=user()['id'];
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_ok()){
 $id=(int)$_POST['id'];
 if($id===$me){ $_SESSION['msg']='Tidak bisa mengubah akun sendiri.'; back(); }
 if($_POST['act']==='role'){ $r=$_POST['role']==='seller'?'seller':'buyer';
  $st=$db->prepare("UPDATE users SET role=? WHERE id=? AND role<>'admin'"); $st->bind_param('si',$r,$id); run($st); }
 if($_POST['act']==='delete'){ $st=$db->prepare("DELETE FROM users WHERE id=? AND role<>'admin'"); $st->bind_param('i',$id);
  if(!run($st)) $_SESSION['msg']='Pengguna tidak bisa dihapus karena punya produk atau pesanan.'; }
 back();
}
panel_start('Pengguna'); echo '<h3>Pengguna</h3><table class="table align-middle"><tr><th>ID</th><th>Nama</th><th>Email</th><th>Peran</th><th></th></tr>';
foreach($db->query("SELECT * FROM users ORDER BY id") as $u){
 echo '<tr><td>'.$u['id'].'</td><td>'.e($u['name']).'</td><td>'.e($u['email']).'</td><td>';
 if($u['role']==='admin'||$u['id']==$me) echo '<span class="badge bg-dark">'.e($u['role']).'</span></td><td></td></tr>'; else {
  echo '<form method="post" class="d-flex gap-1">'.csrf_field().'<input type="hidden" name="id" value="'.$u['id'].'"><input type="hidden" name="act" value="role">
  <select name="role" class="form-select form-select-sm"><option value="buyer"'.($u['role']==='buyer'?' selected':'').'>buyer</option><option value="seller"'.($u['role']==='seller'?' selected':'').'>seller</option></select>
  <button class="btn btn-sm btn-outline-success">Simpan</button></form></td><td>
  <form method="post" onsubmit="return confirm(\'Hapus pengguna?\')">'.csrf_field().'<input type="hidden" name="id" value="'.$u['id'].'"><input type="hidden" name="act" value="delete"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>'; }
}
echo '</table>'; footer_html();