<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$post_name    = $_POST['name'];
	$post_email   = $_POST['email'];
	$post_address = $_POST['address'];

	$db = new Database();
	$result = $db->call('editFB', [
		$_SESSION['info']['id'],
		$post_name,
		$post_email,
		$post_address
	]);
	if(!$result['success']){
		die();
	}

	$_SESSION['info']['name'] = $post_name;
	$_SESSION['info']['email'] = $post_email;
	die();
}

$db = new Database();
$result = $db->call('getFB', [$_SESSION['info']['id']]);
$address = $result['success']&&$result['count']>0?json_decode($result['result'][0]['address'], true):'';

$db = new Database();
$result = $db->call('listOrder', [$_SESSION['info']['id']]);
$orderList = $result['success']&&$result['count']>0?$result['result']:[];

$orderListDic = [];
foreach($orderList as $order){
	$orderListDic[$order['order_code']] = $order;
	$orderListDic[$order['order_code']]['product'] = [];
}

$db = new Database();
$result = $db->call('listOrderProduct', [implode(',', array_keys($orderListDic))]);
$productList = $result['success']&&$result['count']>0?$result['result']:[];

foreach ($productList as $product) {
	$orderListDic[$product['order_code']]['product'][] = $product;
}

?>
<!DOCTYPE html>
<html>
	<head>
		<?php $head_title = $_SESSION['info']['name']; ?>
		<?php include(dirname(__FILE__).'/common/header.php'); ?>
	</head>
	<body>
		<?php include(dirname(__FILE__).'/common/navbar.php'); ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-6 col-lg-4">
					<div class="row pt-3 pb-3">
						<div class="col-6">
							<a href="/cart/" class="btn btn-primary pt-2 pb-2 w-100"><img src="/assets/img/nav/cart_icon.png" height="20"> ตะกร้าสินค้า</a>
						</div>
						<div class="col-6">
							<button class="btn btn-danger pt-2 pb-2 w-100 logoutFB_click"><img src="/assets/img/nav/logout_icon.png" height="20"> ออกจากระบบ</button>
						</div>
					</div>
					<div class="row">
						<div class="col pb-3">
							<div class="card">
							  	<h5 class="card-header">ข้อมูลผู้ใช้</h5>
							  	<div class="card-body" id="info_address">
									<button type="button" class="btn btn-outline-dark btn-sm mb-1" id="profile_edit_click">แก้ไข</button>
							  		<h6 class="card-subtitle font-weight-bold mt-2">ชื่อผู้ใช้</h6>
							  		<p><?= htmlspecialchars($_SESSION['info']['name']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">อีเมล</h6>
							  		<p><?= htmlspecialchars($_SESSION['info']['email']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">ชื่อผู้รับของ</h6>
							  		<p><?= $address['name']==null?'(ยังไม่ได้กรอก)':htmlspecialchars($address['name']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">เบอร์โทรศัพท์</h6>
							  		<p><?= $address['tel']==null?'(ยังไม่ได้กรอก)':htmlspecialchars($address['tel']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">บ้าน ซอย ถนน</h6>
							  		<p><?= $address['place']==null?'(ยังไม่ได้กรอก)':htmlspecialchars($address['place']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">ตำบล / แขวง</h6>
							  		<p><?= $address['subdistrict']==null?'(ยังไม่ได้กรอก)':htmlspecialchars($address['subdistrict']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">อำเภอ / เขต</h6>
							  		<p><?= $address['district']==null?'(ยังไม่ได้กรอก)':htmlspecialchars($address['district']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">จังหวัด</h6>
							  		<p><?= $address['province']==null?'(ยังไม่ได้กรอก)':htmlspecialchars($address['province']) ?></p>
							  		<h6 class="card-subtitle font-weight-bold">รหัสไปรษณีย์</h6>
							  		<p><?= $address['post']==null?'(ยังไม่ได้กรอก)':htmlspecialchars($address['post']) ?></p>
							  	</div>
							  	<div class="card-body hidden" id="form_address">
									<button type="button" class="btn btn-outline-dark btn-sm mb-1 profile_save_click">บันทึก</button>
									<button type="button" class="btn btn-outline-danger btn-sm mb-1 profile_cancel_click">ยกเลิก</button>
							  		<h6 class="card-subtitle font-weight-bold mt-2">ชื่อผู้ใช้</h6>
							  		<input type="text" class="form-control mt-2" id="input_name" value="<?= htmlspecialchars($_SESSION['info']['name']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">อีเมล</h6>
							  		<input type="text" class="form-control mt-2" id="input_email" value="<?= htmlspecialchars($_SESSION['info']['email']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">ชื่อผู้รับของ</h6>
							  		<input type="text" class="form-control mt-2" id="input_name_post" value="<?= $address['name']==null?'':htmlspecialchars($address['name']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">เบอร์โทรศัพท์</h6>
							  		<input type="text" class="form-control mt-2" id="input_tel" value="<?= $address['tel']==null?'':htmlspecialchars($address['tel']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">บ้าน ซอย ถนน</h6>
							  		<input type="text" class="form-control mt-2" id="input_place" value="<?= $address['place']==null?'':htmlspecialchars($address['place']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">ตำบล / แขวง</h6>
							  		<input type="text" class="form-control mt-2" id="input_subdistrict" value="<?= $address['subdistrict']==null?'':htmlspecialchars($address['subdistrict']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">อำเภอ / เขต</h6>
							  		<input type="text" class="form-control mt-2" id="input_district" value="<?= $address['district']==null?'':htmlspecialchars($address['district']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">จังหวัด</h6>
							  		<input type="text" class="form-control mt-2" id="input_province" value="<?= $address['province']==null?'':htmlspecialchars($address['province']) ?>">
							  		<h6 class="card-subtitle font-weight-bold mt-3">รหัสไปรษณีย์</h6>
							  		<input type="text" class="form-control mt-2" id="input_post" value="<?= $address['post']==null?'':htmlspecialchars($address['post']) ?>">
									<button type="button" class="btn btn-outline-dark btn-sm mt-2 profile_save_click">บันทึก</button>
									<button type="button" class="btn btn-outline-danger btn-sm mt-2 profile_cancel_click">ยกเลิก</button>
							  	</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col pt-3 pb-3">
					<div class="card">
					  	<h5 class="card-header">รายการสั่งซื้อทั้งหมด</h5>
					  	<div class="card-body">
					  		<?php if(!count($orderList)): ?>
					  		<div class="alert alert-info" role="alert">
							  <span><strong>ไม่มีรายการสั่งซื้อ</strong></span>
							</div>
							<?php else: foreach($orderList as $order):
								$order['payment_detail'] = json_decode($order['payment_detail'], true);
							?>
							<h5 class="card-header"><?=$order['order_code']?> <span class="badge badge-<?=GEN::ORDER_STAT_COL[$order['status']]?>"><span id="i_order_status"><?=GEN::ORDER_STAT_STR[$order['status']]?></span></span></h5>
							<div class="card-body">
								<div class="row">
									<div class="col-12 col-lg-auto pb-1">
										<table>
											<tr><td>วันที่</td><td><?= date("d/m/y", strtotime($order['order_time'])) ?></td></tr>
											<tr><td>ยอดรวม&nbsp;</td><td>฿<?= number_format($order['payment_detail']['total_price']) ?></td></tr>
											<tr><td colspan="2"><a href="/order/<?=$order['order_code']?>" target="_blank"><?=$order['status']=='BOOK'?'ไปยังหน้าชำระเงิน':'รายละเอียดเพิ่มเติม'?></a></td></tr>
										</table>
									</div>
									<div class="col">
										<div class="row">
											<?php foreach($orderListDic[$order['order_code']]['product'] as $product): ?>
											<div class="col-12 col-xl-6 pb-1">
												<div class="row">
													<div class="img_bg_cov" style="width: 75px;height: 75px;background-image: url('/assets/img/product/s_<?=$product['picture_file']?>');display: inline-block;"></div>
													<div class="col">
										    			<div class="row">
										    				<div class="col">
										    					<?=$product['product_name']?>
										    				</div>
										    			</div>
										    			<div class="row">
												    		<div class="col-6 text-right mw100px">
										    					<?= $product['amount'] ?> ชิ้น
															</div>
												    		<div class="col-6 text-right mw100px">
												    			฿<?= $product['amount']*$product['price'] ?>
												    		</div>
												    	</div>
													</div>
												</div>
											</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
							<?php endforeach; endif; ?>
					  	</div>
					</div>
				</div>
			</div>
		</div>
		<?php include(dirname(__FILE__).'/common/script.php'); ?>
		<script type="text/javascript" src="/assets/js/jquery.Thailand.js/dependencies/zip.js/zip.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>
		<script type="text/javascript" src="/assets/js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>
		<script type="text/javascript">
			$.Thailand({
			    $district: $('#input_subdistrict'),
			    $amphoe: $('#input_district'),
			    $province: $('#input_province'),
			    $zipcode: $('#input_post'),
			});

			$('#profile_edit_click').click(function(){
				$('#info_address').addClass('hidden');
				$('#form_address').removeClass('hidden');
				$('.profile_save_click').click(function(){
					$('.profile_save_click').prop('disabled', true);
					$('.profile_save_click').html('กำลังบันทึก...');
					address = {};
					address.name = $('#input_name_post').val();
					address.tel = $('#input_tel').val();
					address.place = $('#input_place').val();
					address.subdistrict = $('#input_subdistrict').val();
					address.district = $('#input_district').val();
					address.province = $('#input_province').val();
					address.post = $('#input_post').val();
					data = {
						name   :$('#input_name').val(),
						email  :$('#input_email').val(),
						address:JSON.stringify(address)
					};
					$.post('/profile/', data, function(){
						window.location = window.location;
					});
				});
				$('.profile_cancel_click').click(function(){
					window.location = window.location;
				});
			});
		</script>
	</body>
</html>