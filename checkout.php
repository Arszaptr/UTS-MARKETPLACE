<?php require 'common.php';
require_role('buyer');
$cart=$_SESSION['cart']??[]; if(!$cart){ header('Location: /cart.php'); exit; }
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['name']); $phone=trim($_POST['phone']); $addr=trim($_POST['address']);
 if(!$name||!$phone||!$addr) $err='Semua field wajib diisi.';
 else{
  $db->begin_transaction();
  try{
   $items=[]; $total=0;
   foreach($cart as $id=>$qty){
    $st=$db->prepare("SELECT id,price,stock,name FROM products WHERE id=? FOR UPDATE"); $st->bind_param('i',$id); $st->execute();
    $p=$st->get_result()->fetch_assoc();
    if(!$p || $p['stock']<$qty) throw new Exception('Stok tidak cukup: '.($p['name']??'produk'));
    $items[]=[$p['id'],$qty,$p['price']]; $total+=$p['price']*$qty;
   }
   $st=$db->prepare("INSERT INTO orders (buyer_id,customer_name,phone,address,total) VALUES (?,?,?,?,?)");
   $st->bind_param("isssd",$_SESSION["user"]["id"],$name,$phone,$addr,$total); $st->execute(); $oid=$db->insert_id;
   foreach($items as [$pid,$q,$pr]){
    $st=$db->prepare("INSERT INTO order_items (order_id,product_id,qty,price) VALUES (?,?,?,?)"); $st->bind_param('iiid',$oid,$pid,$q,$pr); $st->execute();
    $st=$db->prepare("UPDATE products SET stock=stock-? WHERE id=?"); $st->bind_param('ii',$q,$pid); $st->execute();
   }
   $db->commit(); $_SESSION['cart']=[]; header('Location: /order_success.php?id='.$oid); exit;
  }catch(Exception $ex){ $db->rollback(); $err=$ex->getMessage(); }
 }
}
header_html('Checkout'); ?>
<h3>Checkout</h3><?php if($err) echo '<div class="alert alert-danger">'.e($err).'</div>'; ?>
<form method="post" style="max-width:500px">
<div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" value="<?=e(user()['name'])?>" required></div>
<div class="mb-3"><label class="form-label">No. HP</label><input name="phone" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Alamat</label><textarea name="address" class="form-control" required></textarea></div>
<button class="btn btn-success">Buat Pesanan</button></form>
<?php footer_html();
