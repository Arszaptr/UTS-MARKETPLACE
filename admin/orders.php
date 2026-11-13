<?php require __DIR__.'/../common.php'; require_role('seller'); $uid=user()['id'];
$st=$db->prepare("SELECT o.id,o.customer_name,o.phone,o.address,o.created_at,o.status,p.name pname,i.qty,i.price
 FROM order_items i JOIN orders o ON o.id=i.order_id JOIN products p ON p.id=i.product_id WHERE p.seller_id=? ORDER BY o.id DESC");
$st->bind_param('i',$uid); $st->execute();
header_html('Pesanan Masuk'); echo '<h3>Pesanan Masuk</h3><div class="table-responsive"><table class="table"><tr><th>Order</th><th>Pembeli</th><th>Alamat</th><th>Produk</th><th>Qty</th><th>Subtotal</th><th>Status</th></tr>';
foreach($st->get_result() as $r) echo '<tr><td>#'.$r['id'].'</td><td>'.e($r['customer_name']).'<br><small>'.e($r['phone']).'</small></td><td>'.e($r['address']).'</td><td>'.e($r['pname']).'</td><td>'.$r['qty'].'</td><td>'.rp($r['qty']*$r['price']).'</td><td>'.e($r['status']).'</td></tr>';
echo '</table></div>'; footer_html();
