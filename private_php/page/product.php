<?php

preg_match('/^\/product\/(\w+)(\/([?].*)?)?$/', $request_uri, $match);

$db = new Database();
$result = $db->call('getProduct', [$match[1], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
if(!$result['success']||$result['count']<=0){
	http_response_code(404);
	exit;
}
$product = $result['result'][0];
$product['picture_file'] = explode(' ', $product['picture_file']);
$product['tag_name'] = explode(' ', $product['tag_name']);
foreach ($product['tag_name'] as &$tag) {
	$tag = '<div class="div_ib"><a href="/search/?query='.urlencode('#'.$tag).'">#'.$tag.'</a></div>';
}
$product['tag_name'] = implode(' ', $product['tag_name']);

?>
<!DOCTYPE html>
<html>
	<head>
		<?php $head_title = $product['product_name']; ?>
		<?php include(dirname(__FILE__).'/common/header.php'); ?>
	</head>
	<body>
		<?php include(dirname(__FILE__).'/common/navbar.php'); ?>
		<div class="container-fluid caro_h100">
			<div class="row caro_h100">
			    <div class="col-md bg-dark pl-0 pr-0" id="product_caro">
			    	<a class="a_back text-light" href="<?=isset($_GET['origin'])?$_GET['origin']:'/'?>"><div>< ย้อนกลับ</div></a>
			    	<div id="carouselExampleIndicators" class="carousel slide w-100 caro_h350 " data-ride="carousel" data-interval="false">
					  <ol class="carousel-indicators">
					  	<?php foreach($product['picture_file'] as $pic_i => $picture_file): ?>
						    <li data-target="#carouselExampleIndicators" data-slide-to="<?=$pic_i?>" class="hover_pointer <?=$pic_i==0?'active':''?>"></li>
					    <?php endforeach; ?>
					  </ol>
					  <div class="carousel-inner h-100 w-100">
					  	<?php foreach($product['picture_file'] as $pic_i => $picture_file): ?>
						  	<div class="carousel-item <?=$pic_i==0?'active':''?> h-100 w-100 hover_zin">
						      <div class="d-block h-100 w-100 img_bg" style="background-image: url('/assets/img/product/<?=$picture_file?>');"></div>
						    </div>
					    <?php endforeach; ?>
					  </div>
					  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
					    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
					    <span class="sr-only">Previous</span>
					  </a>
					  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
					    <span class="carousel-control-next-icon" aria-hidden="true"></span>
					    <span class="sr-only">Next</span>
					  </a>
					</div>
			    </div>
			    <div class="col-md-4 ovy_auto">
			    	<div class="row">
			    		<div class="col mt-3">
			    			<h2 id="i_product_name">
			    				<?= $product['product_name'] ?>
			    				<?php if($product['old_price']!=null): ?>
			    				<span class="badge badge-danger">Sale!!!</span>
			    				<?php endif; ?>
			    			</h2>
			    		</div>
			    	</div>
			    	<?php if($product['old_price']!=null): ?>
			    	<div class="row">
			    		<div class="col-12"><span class="ml-2 text-secondary">จากราคา <span style="text-decoration: line-through;">฿<?=number_format($product['old_price'])?></span></span></div>
			    	</div>
			    	<?php endif; ?>
			    	<div class="row">
			    		<div class="col-5 col-md-12 col-lg-5 mt-1 text-center">
			    		<button id="i_price" type="button" class="btn btn-outline-<?=$product['old_price']==null?'info':'danger fs13em'?> w-100" disabled><?= '฿'.number_format($product['price']) ?></button>
			    		</div>
			    		<div class="col-7 col-md-12 col-lg-7 mt-1 text-center">
			    			<button id="i_isIn" data-isIn="<?=$product['amount']>0?>" type="button" class="btn btn-outline-<?=$product['amount']>0?'success':'danger'?> w-100 h-100" disabled><?= $product['amount']>0?'มีของพร้อมส่ง':'สินค้าหมด' ?></button>
			    		</div>
			    	</div>
			    	<div class="row mt-3">
			    		<div id="i_tag_name" class="col text-primary">
			    			<?= $product['tag_name'] ?>
			    		</div>
			    	</div>
			    	<div class="row mt-3">
			    		<div class="col">
			    			<p id="i_product_description">
			    				<?= $product['product_description'] ?>
			    			</p>
			    		</div>
			    	</div>
			    	<?php if($product['amount']>0): ?>
			    		<?php if($_SESSION['role']&ROLE::USER): ?>
				    	<div class="row">
				    		<div class="col text-center p-0">
				    			<?php if($product['is_in_cart']=='1'): ?>
				    			<button type="button" class="list-group-item list-group-item-action rounded-0 border-right-0 h-100" disabled><img src="/assets/img/nav/correct_icon.png" height="20"> ลงตะกร้าแล้ว</button>
				    			<?php else: ?>
				    			<button type="button" class="list-group-item list-group-item-action rounded-0 border-right-0 h-100 hover_pointer i_add_cart_click"><img src="/assets/img/nav/cart_icon.png" height="20"> เพิ่มลงตะกร้า</button>
				    			<?php endif; ?>
				    		</div>
				    		<div class="col text-center p-0">
				    			<button type="button" class="list-group-item list-group-item-action rounded-0 h-100 hover_pointer i_buy_now_click"><img src="/assets/img/nav/money_icon.png" height="20"> ซื้อเลย</button>
				    		</div>
				    	</div>
				    	<?php else: ?>
				    	<div class="row">
				    		<div class="col text-center p-0">
				    			<button type="button" class="btn btn-primary rounded-0 w-100 loginFB_click">
				                	<img src="/assets/img/nav/fb_white.png" height="28"> ล็อกอินเพื่อซื้อสินค้า
				                </button>
				    		</div>
				    	</div>
				    	<?php endif; ?>
			    	<?php endif; ?>
			    </div>
			</div>
		</div>
		<?php include(dirname(__FILE__).'/common/script.php'); ?>
		<script type="text/javascript">
			product_code = '<?=$product["product_code"]?>';
		</script>
		<script type="text/javascript" src="/assets/js/product.js"></script>
	</body>
</html>