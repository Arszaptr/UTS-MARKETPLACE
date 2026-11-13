<?php require 'common.php'; require_role('buyer'); $uid=user()['id'];
$st=$db->prepare("SELECT * FROM orders WHERE buyer_id=? ORDER BY id DESC"); $st->bind_param('i',$uid); $st->execute();
header_html('Pesanan Saya'); echo '<h3>Pesanan Saya</h3><table class="table"><tr><th>No</th><th>Tanggal</th><th>Total</th><th>Status</th><th></th></tr>';
foreach($st->get_result() as $o) echo '<tr><td>#'.$o['id'].'</td><td>'.tgl($o['created_at']).'</td><td>'.rp($o['total']).'</td><td>'.e($o['status']).'</td><td><a href="/order_success.php?id='.$o['id'].'">Detail</a></td></tr>';
echo '</table>'; footer_html();
