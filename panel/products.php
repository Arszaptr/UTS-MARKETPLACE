<?php require __DIR__.'/../common.php'; require __DIR__.'/_nav.php'; require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_ok()){
 $id=(int)$_POST['id']; $st=$db->prepare("DELETE FROM products WHERE id=?"); $st->bind_param('i',$id);
 if(!run($st)) $_SESSION['msg']='Produk sudah ada di pesanan, tidak bisa dihapus.'; back();
}
panel_start('Semua Produk'); echo '<h3>Semua Produk</h3><table class="table"><tr><th>ID</th><th>Nama</th><th>Penjual</th><th>Harga</th><th>Stok</th><th></th></tr>';
foreach($db->query("SELECT p.*,u.name seller FROM products p JOIN users u ON u.id=p.seller_id ORDER BY p.id DESC") as $p)
 echo '<tr><td>'.$p['id'].'</td><td>'.e($p['name']).'</td><td>'.e($p['seller']).'</td><td>'.rp($p['price']).'</td><td>'.$p['stock'].'</td><td>
 <form method="post" onsubmit="return confirm(\'Hapus produk?\')">'.csrf_field().'<input type="hidden" name="id" value="'.$p['id'].'"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>';
echo '</table>'; footer_html();