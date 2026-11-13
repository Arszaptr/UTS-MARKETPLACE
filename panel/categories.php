<?php require __DIR__.'/../common.php'; require __DIR__.'/_nav.php'; require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_ok()){
 if(($_POST['act']??'')==='add' && trim($_POST['name'])!==''){ $n=trim($_POST['name']); $st=$db->prepare("INSERT INTO categories (name) VALUES (?)"); $st->bind_param('s',$n); run($st); }
 if(($_POST['act']??'')==='delete'){ $id=(int)$_POST['id']; $st=$db->prepare("DELETE FROM categories WHERE id=?"); $st->bind_param('i',$id);
  if(!run($st)) $_SESSION['msg']='Kategori masih dipakai produk.'; }
 back();
}
panel_start('Kategori'); echo '<h3>Kategori</h3><form method="post" class="d-flex gap-2 mb-3" style="max-width:400px">'.csrf_field().'<input type="hidden" name="act" value="add"><input name="name" class="form-control" placeholder="Nama kategori" required><button class="btn btn-success">Tambah</button></form>
<table class="table" style="max-width:500px"><tr><th>ID</th><th>Nama</th><th></th></tr>';
foreach($db->query("SELECT * FROM categories ORDER BY id") as $c)
 echo '<tr><td>'.$c['id'].'</td><td>'.e($c['name']).'</td><td><form method="post" onsubmit="return confirm(\'Hapus?\')">'.csrf_field().'<input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="'.$c['id'].'"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>';
echo '</table>'; footer_html();