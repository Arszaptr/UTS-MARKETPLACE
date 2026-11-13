<?php require 'common.php'; require_role('buyer'); $id=(int)($_GET['id']??0); $uid=user()['id'];
$st=$db->prepare("SELECT * FROM orders WHERE id=? AND buyer_id=?"); $st->bind_param('ii',$id,$uid); $st->execute(); $o=$st->get_result()->fetch_assoc();
header_html('Pesanan Berhasil');
if(!$o){ echo 'Pesanan tidak ditemukan.'; footer_html(); exit; }
echo '<div class="alert alert-success">Pesanan #'.$o['id'].' berhasil dibuat atas nama '.e($o['customer_name']).' pada '.tgl($o['created_at']).'. Total: '.rp($o['total']).' (status: '.e($o['status']).')</div>';
$st=$db->prepare("SELECT p.name,i.qty,i.price FROM order_items i JOIN products p ON p.id=i.product_id WHERE i.order_id=?"); $st->bind_param('i',$id); $st->execute();
echo '<table class="table"><tr><th>Produk</th><th>Qty</th><th>Harga</th></tr>';
foreach($st->get_result() as $r) echo '<tr><td>'.e($r['name']).'</td><td>'.$r['qty'].'</td><td>'.rp($r['price']).'</td></tr>';
echo '</table><a href="/index.php" class="btn btn-success">Kembali</a>'; footer_html();
