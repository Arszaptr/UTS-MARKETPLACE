<?php require __DIR__.'/../common.php'; require __DIR__.'/_nav.php'; panel_start('Dashboard Admin');
$c=fn($sql)=>$db->query($sql)->fetch_row()[0];
$s=[['Pengguna',$c("SELECT COUNT(*) FROM users"),'bi-people'],['Produk',$c("SELECT COUNT(*) FROM products"),'bi-box'],
 ['Pesanan',$c("SELECT COUNT(*) FROM orders"),'bi-receipt'],['Pendapatan',rp($c("SELECT COALESCE(SUM(total),0) FROM orders")),'bi-cash']];
echo '<h3>Dashboard Admin</h3><div class="row">';
foreach($s as [$l,$v,$i]) echo '<div class="col-6 col-md-3 mb-3"><div class="card border-0 shadow-sm"><div class="card-body"><i class="bi '.$i.' fs-3 text-success"></i><div class="text-muted">'.$l.'</div><h4>'.e($v).'</h4></div></div></div>';
echo '</div>'; footer_html();