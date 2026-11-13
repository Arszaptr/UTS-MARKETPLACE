<?php require __DIR__.'/../common.php'; require_role('seller'); $sid=user()['id'];
$uploadDir=__DIR__.'/../uploads/';
function save_image($f,&$err){
 global $uploadDir;
 if(!isset($f['error']) || $f['error']===UPLOAD_ERR_NO_FILE) return null;
 if($f['error']!==UPLOAD_ERR_OK){ $err='Upload gagal (kode '.$f['error'].').'; return false; }
 if($f['size']>2*1024*1024){ $err='Ukuran gambar maksimal 2 MB.'; return false; }
 $info=@getimagesize($f['tmp_name']);
 $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$info['mime'] ?? ''] ?? null;
 if(!$ext){ $err='Format gambar harus JPG, PNG, atau WEBP.'; return false; }
 if(!is_dir($uploadDir)) @mkdir($uploadDir,0755,true);
 $name=bin2hex(random_bytes(8)).'.'.$ext;
 if(!move_uploaded_file($f['tmp_name'],$uploadDir.$name)){ $err='Gagal menyimpan file. Pastikan folder uploads ada di htdocs.'; return false; }
 return '/uploads/'.$name;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_ok()){ $_SESSION['msg']='Sesi tidak valid atau file terlalu besar, coba lagi dengan gambar yang lebih kecil.'; header('Location: products.php'); exit; }
 $id=(int)$_POST['id']; $cat=(int)$_POST['category_id']; $n=trim($_POST['name']); $d=trim($_POST['description']);
 $pr=(float)$_POST['price']; $s=(int)$_POST['stock'];
 $err=''; $new=save_image($_FILES['image'] ?? [],$err);
 if($new===false){ $_SESSION['msg']=$err; header('Location: products.php'.($id?'?edit='.$id:'')); exit; }
 $img=$new ?? trim($_POST['old_image'] ?? '');
 if($id){ $st=$db->prepare("UPDATE products SET category_id=?,name=?,description=?,price=?,stock=?,image_url=? WHERE id=? AND seller_id=?"); $st->bind_param('issdisii',$cat,$n,$d,$pr,$s,$img,$id,$sid); }
 else { $st=$db->prepare("INSERT INTO products (category_id,seller_id,name,description,price,stock,image_url) VALUES (?,?,?,?,?,?,?)"); $st->bind_param('iissdis',$cat,$sid,$n,$d,$pr,$s,$img); }
 $st->execute(); header('Location: products.php'); exit;
}
if(isset($_GET['delete'])){ $id=(int)$_GET['delete'];
 $q=$db->prepare("SELECT image_url FROM products WHERE id=? AND seller_id=?"); $q->bind_param('ii',$id,$sid); $q->execute(); $old=$q->get_result()->fetch_assoc();
 try{ $st=$db->prepare("DELETE FROM products WHERE id=? AND seller_id=?"); $st->bind_param('ii',$id,$sid); $st->execute();
  if($st->affected_rows && $old && strpos($old['image_url'],'/uploads/')===0){ @unlink($uploadDir.basename($old['image_url'])); } }
 catch(mysqli_sql_exception $x){ $_SESSION['msg']='Produk sudah ada di pesanan, tidak bisa dihapus.'; }
 header('Location: products.php'); exit; }
$edit=['id'=>0,'category_id'=>1,'name'=>'','description'=>'','price'=>'','stock'=>'','image_url'=>''];
if(isset($_GET['edit'])){ $st=$db->prepare("SELECT * FROM products WHERE id=? AND seller_id=?"); $st->bind_param('ii',$_GET['edit'],$sid); $st->execute(); $edit=$st->get_result()->fetch_assoc() ?: $edit; }
header_html('Produk Saya');
if(!empty($_SESSION['msg'])){ echo '<div class="alert alert-warning">'.e($_SESSION['msg']).'</div>'; unset($_SESSION['msg']); }
?>
<h3>Kelola Produk Saya</h3>
<form method="post" enctype="multipart/form-data" class="row g-2 mb-4 border p-3 rounded"><?=csrf_field()?><input type="hidden" name="id" value="<?=$edit['id']?>"><input type="hidden" name="old_image" value="<?=e($edit['image_url'])?>">
<div class="col-md-4"><input name="name" class="form-control" placeholder="Nama" value="<?=e($edit['name'])?>" required></div>
<div class="col-md-3"><select name="category_id" class="form-select"><?php foreach($db->query("SELECT * FROM categories") as $c) echo '<option value="'.$c['id'].'"'.($c['id']==$edit['category_id']?' selected':'').'>'.e($c['name']).'</option>'; ?></select></div>
<div class="col-md-2"><input name="price" type="number" step="0.01" class="form-control" placeholder="Harga" value="<?=e($edit['price'])?>" required></div>
<div class="col-md-2"><input name="stock" type="number" class="form-control" placeholder="Stok" value="<?=e($edit['stock'])?>" required></div>
<div class="col-12"><label class="form-label small mb-1">Gambar produk (JPG/PNG/WEBP, maks. 2 MB)</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control">
 <?php if($edit['image_url']) echo '<img src="'.e($edit['image_url']).'" height="60" class="mt-2 rounded" alt=""> <small class="text-muted">Gambar saat ini (kosongkan upload untuk mempertahankan)</small>'; ?></div>
<div class="col-12"><input name="description" class="form-control" placeholder="Deskripsi" value="<?=e($edit['description'])?>"></div>
<div class="col-12"><button class="btn btn-success"><?=$edit['id']?'Update':'Tambah'?> Produk</button> <?php if($edit['id']) echo '<a href="products.php" class="btn btn-outline-secondary">Batal</a>'; ?></div></form>
<table class="table align-middle"><tr><th>Gambar</th><th>Nama</th><th>Harga</th><th>Stok</th><th></th></tr>
<?php $st=$db->prepare("SELECT * FROM products WHERE seller_id=? ORDER BY id DESC"); $st->bind_param('i',$sid); $st->execute();
foreach($st->get_result() as $p){ ?>
<tr><td><?php if($p['image_url']) echo '<img src="'.e($p['image_url']).'" height="45" class="rounded" alt="">'; ?></td><td><?=e($p['name'])?></td><td><?=rp($p['price'])?></td><td><?=$p['stock']?></td>
<td><a href="?edit=<?=$p['id']?>" class="btn btn-sm btn-warning">Edit</a> <a href="?delete=<?=$p['id']?>" onclick="return confirm('Hapus?')" class="btn btn-sm btn-danger">Hapus</a></td></tr>
<?php } echo '</table>'; footer_html();