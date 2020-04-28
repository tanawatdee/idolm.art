<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	if($_POST['action']=='add'){
		$db = new Database();
		$result = $db->call('addCart', [$_SESSION['info']['id'], $_POST['product_code']]);
		die();
	}
	else if($_POST['action']=='del'){
		$db = new Database();
		$result = $db->call('delCart', [$_SESSION['info']['id'], $_POST['product_code']]);
		die();
	}
	else if($_POST['action']=='edit'){
		$db = new Database();
		$result = $db->call('editCart', [$_SESSION['info']['id'], $_POST['product_code'], $_POST['amount']]);
		die();
	}
	else if($_POST['action']=='delivery'){
		$_SESSION['info']['delivery'] = $_POST['type'];
		die();
	}
	else if($_POST['action']=='order'){
		$order_code = date('md');
		$pool = array_merge(range(0,9),range('A', 'Z'));
	    for($i=0; $i < 6; $i++) {
	        $order_code .= $pool[mt_rand(0, 35)];
	    }
		$fbid = $_SESSION['info']['id'];
		$delivery_type = $_POST['delivery'];
		$delivery_fee = GEN::DELIVERY[$delivery_type];
		$order_exp_str = GEN::ORDER_EXP_STR;

		$db = new Database();
		$result = $db->call('newOrder', [
			$order_code,
			$fbid,
		    $delivery_type,
		    $delivery_fee,
		    $order_exp_str
		]);
		if(!$result['success']){
			die(json_encode(['success'=>false, 'err_code'=>'ERR_DB']));
		}
		else if($result['count']>0){
			die(json_encode(['success'=>false, 'err_code'=>'ERR_LIMIT', 'product'=>$result['result']]));
		}
		else{
			die(json_encode(['success'=>true, 'order_code'=>$order_code]));
		}
		die();
	}
}

$_SESSION['info']['delivery'] = isset($_SESSION['info']['delivery'])?$_SESSION['info']['delivery']:key(GEN::DELIVERY);
$delivery_type = $_SESSION['info']['delivery'];

$db = new Database();
$result = $db->call('listCart', [$_SESSION['info']['id']]);

$products = (!$result['success']||$result['count']<=0)?[]:$result['result'];

?>
<!DOCTYPE html>
<html>
	<head>
		<?php $head_title = 'ตะกร้าสินค้า'; ?>
		<?php include(dirname(__FILE__).'/common/header.php'); ?>
	</head>
	<body>
		<?php include(dirname(__FILE__).'/common/navbar.php'); ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col mt-3"><h1>ตะกร้าสินค้า</h1></div>
			</div>
			<div class="row">
				<?php foreach($products as $product): ?>
				<div class="col-12 col-md-6 col-lg-4 card border-primary rounded-0 bg-light i_product_item" data-code="<?= $product['product_code'] ?>">
					<div class="del_product del_product_click" data-code="<?= $product['product_code'] ?>">X</div>
			    	<div class="row h-100">
			    		<div class="col-3 img_bg_cov i_product_img" style="background-image: url('/assets/img/product/<?=explode(' ', $product['picture_file'])[0] ?>');"></div>
			    		<div class="col-9 card-body text-primary">
			    			<div class="row">
			    				<div class="col">
			    					<h5 class="i_product_name"><?= $product['product_name'] ?></h5>
			    				</div>
			    			</div>
			    			<div class="row">
			    				<div class="col-6">
			    					<form onsubmit="return false;">
				    					<div class="input-group">
										  <input type="number" class="form-control i_amount" value="<?= $product['amount'] ?>" min="1" max="<?= $product['max_amount'] ?>" required data-code="<?= $product['product_code'] ?>" data-price="<?= $product['price'] ?>">
										  <div class="input-group-append">
										    <span class="input-group-text">ชิ้น</span>
										  </div>
										</div>
									</form>
			    				</div>
			    				<div class="col-6"><h5 class="text-right mt-2 mr-2">฿<span class="item_subtotal"></span></h5></div>
			    			</div>
			    		</div>
			    	</div>
            	</div>
            	<?php endforeach; ?>
			</div>
			<?php if(count($products)): ?>
			<div class="row border-top pt-2 mt-2">
				<div class="col-12 col-md-6 col-lg-4 card border-primary rounded-0 bg-light">
			    	<div class="row">
			    		<div class="col-7 card-body text-primary">
	    					<div class="input-group">
							  	<div class="input-group-prepend">
							    	<span class="input-group-text">ค่าจัดส่ง</span>
							  	</div>
							  	<select id="delivery_sel" name="delivery_sel" class="form-control">
								  	<?php foreach(GEN::DELIVERY_STR as $i=>$delivery): ?>
							  			<option value="<?=$i?>" <?= $i==$delivery_type?'selected':'' ?>><?= $delivery ?></option>
							  		<?php endforeach; ?>
								</select>
							</div>
						</div>
			    		<div class="col-5 card-body text-primary">
			    			<h5 class="text-right mt-2 mr-2">฿<span id="delivery_fee"></span></h5>
			    		</div>
			    	</div>
            	</div>
			</div>
			<div class="row border-top pt-2 mt-2">
				<div class="col-12 col-md-6 col-lg-4 card border-success rounded-0 bg-light">
			    	<div class="row">
			    		<div class="col-7 card-body text-success">
	    					<h5 class=" ml-2 mt-2">รวมทั้งหมด</h5>
						</div>
			    		<div class="col-5 card-body text-success">
			    			<h5 class="text-right mt-2 mr-2">฿<span id="total_price"></span></h5>
			    		</div>
			    	</div>
            	</div>
            	<div class="col-12 col-lg-8 mt-2 hidden" id="err_box">
					<div class="alert alert-danger" role="alert">
					  <span id="err_txt"></span>
					</div>
				</div>
            	<div class="col-12 col-lg-8">
			    	<div class="row">
			    		<div class="col-8 card-body"><h6><span id="disclaimer_text">เมื่อกดสั่งซื้อแล้ว หากไม่ชำระเงินด้วยบัตรเครดิตหรือไม่ส่งหลักฐานการโอนเงิน <span class="text-danger">ภายใน 2 ชั่วโมง</span> รายการสั่งซื้อจะถูกยกเลิก</span></h6></div>
			    		<div class="col-4 mt-2">
			    			<div class="row">
			    				<div class="col-auto my-1">
							      <div class="custom-control custom-checkbox mr-sm-2">
							        <input type="checkbox" class="custom-control-input" id="disclaimer_check" name="disclaimer_check">
							        <label class="custom-control-label" for="disclaimer_check">ยอมรับ</label>
							      </div>
							    </div>
			    			</div>
			    			<div class="row">
			    				<div class="col"><button type="button" class="btn btn-success" id="buy_click" disabled>สั่งซื้อ</button></div>
			    			</div>
			    		</div>
			    	</div>
            	</div>
			</div>
			<?php else: ?>
			<div class="row">
				<div class="col">
					<div id="no_product" class="alert alert-info" role="alert">
					  <strong>ไม่มีสินค้าในตะกร้า</strong> 
					  <a href="/">กลับหน้าแรก</a>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php include(dirname(__FILE__).'/common/script.php'); ?>
		<script type="text/javascript">
			delivery_rate = <?= json_encode(GEN::DELIVERY) ?>;
		</script>
		<script type="text/javascript" src="/assets/js/cart.js"></script>
	</body>
</html>