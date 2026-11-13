<?php require __DIR__.'/../common.php'; require __DIR__.'/_nav.php'; require_role('admin');
if($_SERVER['REQUEST_METHOD']==='POST' && csrf_ok()){
 $id=(int)$_POST['id']; $s=in_array($_POST['status'],['pending','paid','shipped','done'])?$_POST['status']:'pending';
 $st=$db->prepare("UPDATE orders SET status=? WHERE id=?"); $st->bind_param('si',$s,$id); run($st); back();
}
panel_start('Semua Pesanan'); echo '<h3>Semua Pesanan</h3><div class="table-responsive"><table class="table align-middle"><tr><th>No</th><th>Pembeli</th><th>Tanggal</th><th>Total</th><th>Status</th></tr>';
foreach($db->query("SELECT * FROM orders ORDER BY id DESC") as $o){
 echo '<tr><td>#'.$o['id'].'</td><td>'.e($o['customer_name']).'<br><small>'.e($o['phone']).'</small></td><td>'.tgl($o['created_at']).'</td><td>'.rp($o['total']).'</td><td>
 <form method="post" class="d-flex gap-1">'.csrf_field().'<input type="hidden" name="id" value="'.$o['id'].'"><select name="status" class="form-select form-select-sm">';
 foreach(['pending','paid','shipped','done'] as $s) echo '<option'.($o['status']===$s?' selected':'').'>'.$s.'</option>';
 echo '</select><button class="btn btn-sm btn-outline-success">Ubah</button></form></td></tr>';
}
echo '</table></div>'; footer_html();