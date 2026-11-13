<?php require 'common.php';
$id=(int)($_GET['id']??0);
$st=$db->prepare("SELECT p.*, c.name cat, u.name seller FROM products p JOIN categories c ON c.id=p.category_id JOIN users u ON u.id=p.seller_id WHERE p.id=?");
$st->bind_param('i',$id); $st->execute(); $p=$st->get_result()->fetch_assoc();
header_html('Detail Produk');
if(!$p){ echo '<div class="alert alert-warning">Produk tidak ditemukan.</div>'; footer_html(); exit; } ?>
<div class="row"><div class="col-md-5"><img src="<?=e($p['image_url'])?>" class="img-fluid rounded" alt=""></div>
<div class="col-md-7"><h3><?=e($p['name'])?></h3><p class="text-muted"><?=e($p['cat'])?> · Penjual: <?=e($p['seller'])?></p>
<h4 class="text-success"><?=rp($p['price'])?></h4><p><?=nl2br(e($p['description']))?></p><p>Stok: <?=$p['stock']?></p>
<?php if($p['stock']>0){ ?>
<form method="post" action="/cart.php" class="d-flex gap-2" style="max-width:260px">
<input type="hidden" name="action" value="add"><input type="hidden" name="id" value="<?=$p['id']?>">
<input type="number" name="qty" value="1" min="1" max="<?=$p['stock']?>" class="form-control">
<button class="btn btn-success">+ Keranjang</button></form>
<?php } else echo '<span class="badge bg-danger">Stok habis</span>'; ?>
</div></div>
<?php footer_html();
