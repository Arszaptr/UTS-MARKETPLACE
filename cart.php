<?php require 'common.php';
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
if($_SERVER['REQUEST_METHOD']==='POST'){
 if((user()['role']??'')==='seller'){ header('Location: /index.php'); exit; }
 $a=$_POST['action']??''; $id=(int)($_POST['id']??0);
 if($a==='add'){ $_SESSION['cart'][$id]=($_SESSION['cart'][$id]??0)+max(1,(int)$_POST['qty']); }
 if($a==='update'){ $q=(int)$_POST['qty']; if($q>0)$_SESSION['cart'][$id]=$q; else unset($_SESSION['cart'][$id]); }
 if($a==='remove'){ unset($_SESSION['cart'][$id]); }
 header('Location: /cart.php'); exit;
}
header_html('Keranjang'); echo '<h3>Keranjang Belanja</h3>';
$total=0;
if(!$_SESSION['cart']){ echo '<div class="alert alert-info">Keranjang kosong. <a href="/index.php">Belanja dulu</a></div>'; footer_html(); exit; }
echo '<table class="table align-middle"><tr><th>Produk</th><th>Harga</th><th width="140">Qty</th><th>Subtotal</th><th></th></tr>';
foreach($_SESSION['cart'] as $id=>$qty){
 $st=$db->prepare("SELECT name,price FROM products WHERE id=?"); $st->bind_param('i',$id); $st->execute();
 $p=$st->get_result()->fetch_assoc(); if(!$p) continue; $sub=$p['price']*$qty; $total+=$sub; ?>
<tr><td><?=e($p['name'])?></td><td><?=rp($p['price'])?></td>
<td><form method="post" class="d-flex gap-1"><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?=$id?>">
<input type="number" name="qty" value="<?=$qty?>" min="0" class="form-control form-control-sm"><button class="btn btn-sm btn-outline-secondary">OK</button></form></td>
<td><?=rp($sub)?></td>
<td><form method="post"><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?=$id?>"><button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>
<?php } ?>
<tr><th colspan="3" class="text-end">Total</th><th><?=rp($total)?></th><th></th></tr></table>
<a href="/index.php" class="btn btn-outline-success">Lanjut Belanja</a> <a href="/checkout.php" class="btn btn-success">Checkout</a>
<?php footer_html();
