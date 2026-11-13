<?php
function panel_start($title){
 require_role('admin'); header_html($title);
 $cur=basename($_SERVER['SCRIPT_NAME']);
 $m=['index.php'=>'Dashboard','users.php'=>'Pengguna','products.php'=>'Produk','orders.php'=>'Pesanan','categories.php'=>'Kategori'];
 echo '<ul class="nav nav-pills mb-4">';
 foreach($m as $f=>$l) echo '<li class="nav-item"><a class="nav-link '.($cur===$f?'active bg-success':'text-success').'" href="/panel/'.$f.'">'.$l.'</a></li>';
 echo '</ul>';
 if(!empty($_SESSION['msg'])){ echo '<div class="alert alert-warning">'.e($_SESSION['msg']).'</div>'; unset($_SESSION['msg']); }
}
function run($st){ try{ return $st->execute(); }catch(Throwable $x){ return false; } }
function back(){ header('Location: '.$_SERVER['SCRIPT_NAME']); exit; }