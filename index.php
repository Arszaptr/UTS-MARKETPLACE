<?php require 'common.php'; header_html('Daftar Produk');
if(!user()) echo '<div class="p-4 mb-4 bg-light rounded-3 border"><h2>Selamat datang di Pasar Online</h2><p class="mb-3 text-muted">Belanja produk tradisional langsung dari penjual.</p><a href="/register.php" class="btn btn-success">Daftar Gratis</a> <a href="/login.php" class="btn btn-outline-success">Masuk</a></div>';
$res = $db->query("SELECT p.*, c.name cat FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC");
echo '<h3>Daftar Produk</h3><div class="row">';
while($p=$res->fetch_assoc()){ ?>
<div class="col-6 col-md-4 col-lg-3 mb-4"><div class="card h-100">
 <img src="<?=e($p['image_url'])?>" class="card-img-top" alt="" style="height:220px;width:100%;object-fit:cover;">
 <div class="card-body"><span class="badge bg-secondary"><?=e($p['cat'])?></span>
 <h6 class="mt-2"><?=e($p['name'])?></h6><p class="fw-bold text-success"><?=rp($p['price'])?></p></div>
 <div class="card-footer bg-white"><a href="/detail.php?id=<?=$p['id']?>" class="btn btn-success btn-sm w-100">Lihat Detail</a></div>
</div></div>
<?php } echo '</div>'; footer_html();
