<?php

preg_match('/^\/order\/(\w+)(\/([?].*)?)?$/', $request_uri, $match);

$db = new Database();
$result = $db->call('getOrder', [$match[1], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
if(!$result['success']||$result['count']<=0){
	http_response_code(404);
	exit;
}
$order_info = $result['result'][0];
$order_info['payment_detail'] = json_decode($order_info['payment_detail'], true);
$order_info['badge_type'] = GEN::ORDER_STAT_COL[$order_info['status']];
$order_info['address_show'] = $order_info['status']=='BOOK'?$order_info['address']:$order_info['payment_detail']['address'];
$address = json_decode($order_info['address_show'], true);

$result = $db->call('getOrder_product', [$match[1], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
if(!$result['success']||$result['count']<=0){
	http_response_code(404);
	exit;
}
$products = $result['result'];

?>
<!DOCTYPE html>
<html>
	<head>
		<?php $head_title = 'รายการสั่งซื้อ '.$order_info['order_code']; ?>
		<?php include(dirname(__FILE__).'/common/header.php'); ?>
	</head>
	<body>
		<?php include(dirname(__FILE__).'/common/navbar.php'); ?>
		<div class="container-fluid">
			<?php if(isset($_GET['error'])): ?>
			<div class="row">
				<div class="col mt-2">
					<div class="alert alert-danger" role="alert">
					  <span id="block_error"><strong><?= $_GET['error'] ?></strong></span>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<div class="row">
				<div class="col mt-3"><h1>รายการสั่งซื้อ <span id="i_order_code"><?= $order_info['order_code'] ?></span> <span class="badge badge-<?= $order_info['badge_type'] ?>"><span id="i_order_status"><?= GEN::ORDER_STAT_STR[$order_info['status']] ?></span></span><h1></div>
			</div>
			<?php if($order_info['status']=='SENT'): ?>
			<div class="row">
				<div class="col mt-2">
					<div class="alert alert-info" role="alert">
					  <span><strong>เลขแทร็ก </strong><span id="i_order_track"><?= $order_info['tracking_no'] ?></span></span>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<div class="row">
				<?php foreach($products as $product): ?>
				<div class="col-12 col-md-6 col-lg-4 card border-primary rounded-0 bg-light i_product_item" data-code="<?= $product['product_code'] ?>">
			    	<div class="row h-100">
			    		<div class="col-3 img_bg_cov i_product_img" style="background-image: url('/assets/img/product/<?= $product['picture_file'] ?>');"></div>
			    		<div class="col-9 card-body text-primary">
			    			<div class="row">
			    				<div class="col">
			    					<h5 class="i_product_name"><?= $product['product_name'] ?></h5>
			    				</div>
			    			</div>
			    			<div class="row">
					    		<div class="col-6">
			    					<h5 class="text-right mt-2 mr-2 i_amount" data-amount="<?= $product['amount'] ?>" data-price="<?= $product['price'] ?>"><?= $product['amount'] ?> ชิ้น</h5>
								</div>
					    		<div class="col-6">
					    			<h5 class="text-right mt-2 mr-2 item_subtotal">฿<?= $product['amount']*$product['price'] ?></h5>
					    		</div>
					    	</div>
			    		</div>
			    	</div>
            	</div>
            	<?php endforeach; ?>
			</div>
			<div class="row border-top pt-2 mt-2">
				<div class="col-12 col-md-6 col-lg-4 card border-primary rounded-0 bg-light">
			    	<div class="row">
			    		<div class="col-8 card-body text-primary">
	    					<h5 class="ml-2 mt-2">ค่าจัดส่ง <?=GEN::DELIVERY_STR[$order_info['delivery_type']]?></h5>
						</div>
			    		<div class="col-4 card-body text-primary">
			    			<h5 class="text-right mt-2 mr-2" id="i_delivery_fee" data-fee="<?= $order_info['delivery_fee'] ?>">฿<?= $order_info['delivery_fee'] ?></h5>
			    		</div>
			    	</div>
            	</div>
            	<?php if($order_info['discount']!=0): ?>
            	<div class="col-12 col-md-6 col-lg-4 card border-primary rounded-0 bg-light">
			    	<div class="row">
			    		<div class="col-7 card-body text-primary">
	    					<h5 class=" ml-2 mt-2">ส่วนลด</h5>
						</div>
			    		<div class="col-5 card-body text-primary">
			    			<h5 class="text-right mt-2 mr-2" id="i_discount" data-discount="<?= $order_info['discount'] ?>">฿-<?= $order_info['discount'] ?></h5>
			    		</div>
			    	</div>
            	</div>
            	<?php endif; ?>
			</div>
			<div class="row border-top pt-2 mt-2">
				<div class="col-12 col-md-6 col-lg-4 card border-success rounded-0 bg-light">
			    	<div class="row">
			    		<div class="col-7 card-body text-success">
	    					<h5 class=" ml-2 mt-2">รวมทั้งหมด</h5>
						</div>
			    		<div class="col-5 card-body text-success">
			    			<h5 class="text-right mt-2 mr-2" id="total_price">฿<?= $order_info['payment_detail']['total_price'] ?></h5>
			    		</div>
			    	</div>
            	</div>
			</div>
			<?php if($order_info['status']!='FAIL'): ?>
			<div class="row border-top pt-2 mt-2">
				<div class="col mb-3">
			    	<div class="card">
					  	<h5 class="card-header">ชื่อ เบอร์โทรศัพท์ ที่อยู่ และรหัสไปรษณีย์</h5>
					  	<div class="card-body" id="info_address">
							<?php if($order_info['status']=='BOOK'): ?>
							<button type="button" class="btn btn-outline-dark btn-sm mb-3" id="profile_edit_click">แก้ไข</button>
							<?php endif; ?>
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
            	<?php if($order_info['status']=='BOOK'): ?>
            	<div class="col-12 col-md-6 col-lg-8 mb-3">
			    	<div class="card">
						<h5 class="card-header">การชำระเงิน</h5>
						<div class="card-body">
							<!-- <div class="row">
								<div class="col">
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="pay_method_transfer" name="pay_method" class="custom-control-input" value="transfer">
										<label class="custom-control-label" for="pay_method_transfer">โอนเงินธนาคาร</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="pay_method_credit" name="pay_method" class="custom-control-input" value="credit">
										<label class="custom-control-label" for="pay_method_credit">บัตรเครดิต</label>
									</div>
								</div>
							</div> -->
							<div class="row mt-3 pay_choice" id="choice_transfer">
								<div class="col">
									<form id="form_transfer" onsubmit="return false;">
										<input type="hidden" name="order_code" value="<?= $order_info['order_code'] ?>">
				  						<input type="hidden" name="order_address" value="<?= htmlspecialchars($order_info['address']) ?>">

										<p>โอนเงินมายังบัญชีใดบัญชีหนึ่งต่อไปนี้</p>
										<ul>
											<?php foreach(GEN::BANK_STR as $bank): ?>
												<li><?= $bank ?></li>
											<?php endforeach; ?>
										</ul>
										<p>จากนั้นกรอกข้อมูลแจ้งชำระเงินภายในวันที่<span class="text-info"> <?= date("d/m/y เวลา H:i น.", strtotime($order_info['expire_time'])) ?></span></p>
										<div class="input-group">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w140px">ธนาคารปลายทาง</span>
										  	</div>
											<select class="form-control" name="target_bank" required>
											  	<?php foreach(GEN::BANK_STR as $i=>$bank): ?>
										  			<option value="<?=$i?>"><?= $bank ?></option>
										  		<?php endforeach; ?>
											</select>
										</div>
										<div class="input-group">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w140px">วันที่โอน</span>
										  	</div>
											<input type="date" class="form-control" name="transfer_date" required>
										</div>
										<div class="input-group">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w140px">เวลาที่โอน</span>
										  	</div>
											<input type="time" class="form-control" name="transfer_time" required>
										</div>
										<div class="input-group">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w140px">จำนวนเงิน</span>
										  	</div>
											<input type="number" step="0.01" class="form-control" value="<?= $order_info['payment_detail']['total_price'].'.00' ?>" name="transfer_amount" required>
										</div>
										<div class="input-group">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w140px">หลักฐานการโอน</span>
										  	</div>
										  	<div class="custom-file">
										    	<input type="file" class="custom-file-input" id="transfer_pic" name="transfer_pic" accept="image/*" required>
										    	<label class="custom-file-label text-truncate" for="transfer_pic">ไฟล์ภาพ</label>
										  	</div>
										</div>
										<div class="alert alert-danger mt-2 hidden" role="alert" id="transfer_errors_box">
											<strong><span id="transfer_errors">หมายเลขบัตรไม่ถูกต้อง</span></strong>
										</div>
										<div class="row">
											<div class="col mt-2 text-center">
												<button class="btn btn-success" id="transfer_click">แจ้งชำระเงิน</button>
												<button id="transfer_processing"  class="btn btn-success hidden" disabled>กำลังดำเนินการ...</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="row mt-3 pay_choice hidden" id="choice_credit">
								<div class="col">
									<form action="/omise/" method="post" id="checkout">
										<input type="hidden" name="omise_token">
										<input type="hidden" name="order_code" value="<?= $order_info['order_code'] ?>">
										<input type="hidden" name="order_address" value="<?= htmlspecialchars($order_info['address']) ?>">

										<div class="row">
											<div class="col pb-2">
												<img src="/assets/img/nav/credit.png" class="img_credit_logo">
												<span class="align-middle text-muted"> ให้บริการโดย </span>
												<img src="/assets/img/nav/omise.png" class="img_credit_logo">
											</div>
										</div>
										<div class="input-group" style="max-width: 400px;">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text">จำนวนเงินที่ต้องชำระ</span>
										  	</div>
											<input type="text" class="form-control" disabled value="฿<?= $order_info['payment_detail']['total_price'] ?>.00">
										</div>
										<div class="input-group" style="max-width: 400px;">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w110px">หมายเลขบัตร</span>
										  	</div>
											<input type="text" class="form-control" id="credit_no_input" data-omise="number">
										</div>
										<div class="input-group" style="max-width: 400px;">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w110px">ชื่อผู้ถือบัตร</span>
										  	</div>
											<input type="text" class="form-control text-uppercase" data-omise="holder_name">
										</div>
										<div class="input-group" style="max-width: 300px;">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w110px">หมดอายุ</span>
										  	</div>
											<input type="text" class="form-control" data-omise="expiration_month">
											<span class="input-group-text rounded-0">/</span>
											<input type="text" class="form-control" data-omise="expiration_year">
										</div>
										<div class="input-group mb-3" style="max-width: 187px;">
										  	<div class="input-group-prepend">
										    	<span class="input-group-text w110px hover_default" id="cvv_pop">CVV &emsp;<img src="/assets/img/nav/q-icon.png" class="img_credit_logo"></span>
										  	</div>
										    <input type="text" class="form-control" data-omise="security_code">
										    
										</div>
										<div class="row hidden" id="block_refund_policy">
											<div class="col alert alert-secondary" role="alert">
												<h5>นโยบายการเปลี่ยนสินค้าและคืนเงิน</h5>
									            <ol>
									                <li>สามารถเปลี่ยนสินค้าได้ภายใน 7 วันหลังได้รับสินค้า โดยแจ้งเข้ามาทาง line@ idolmart(@lgc7279j) หรือ E-mail support@idolm.art</li>
									                <li>ต้องส่งหลักฐานประกอบการใช้สิทธิ์มาให้ชัดเจน เป็นไทม์แลป วิดีโอ หรือภาพถ่ายอย่างน้อย 3 ภาพให้ชัดเจน (ภายนอก ภายใน สภาพสินค้า) โดยจะรับเปลี่ยนสินค้าที่มีตำหนิจากการส่งสินค้าเท่านั้น</li>
									                <li>หากยังมีสินค้าอยู่ภายในสต็อกของทางเว็บไซต์ จะรับประกันโดยการเปลี่ยนสินค้าแบบเดียวกันให้ หากไม่มีจึงใช้วิธีการคืนเงิน</li>
									            </ol>
											</div>
										</div>
										<div class="row">
											<div class="col hover_pointer" id="block_agreement"><h6>การคลิกปุ่มยืนยันการชำระเงิน ถือว่าเป็นการยอมรับ <strong>นโยบายการให้บริการ</strong></h6></div>
										</div>
										<div class="alert alert-danger hidden" id="token_errors_box" role="alert">
										  	<strong><span id="token_errors">หมายเลขบัตรไม่ถูกต้อง</span></strong>
										</div>
										<div class="row">
											<div class="col" style="margin-left: 80px;">
												<input type="submit" class="btn btn-success" id="create_token" value="ยืนยันการชำระเงิน">
												<button class="btn btn-success hidden" id="credit_processing" disabled>กำลังดำเนินการ...</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
            	</div>
            	<?php endif; ?>
			</div>
			<?php else: ?>
			<div class="row">
				<div class="col mt-2">
					<div class="alert alert-danger" role="alert">
					  <span id="block_error"><strong>รายการสั่งซื้อถูกยกเลิกเนื่องจากไม่ชำระเงินในเวลาที่กำหนด</strong></span>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php include(dirname(__FILE__).'/common/script.php'); ?>
		<script src="https://cdn.omise.co/omise.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
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
				$('#profile_edit_click').addClass('hidden');
				$('#profile_save_click, #profile_cancel_click').removeClass('hidden');
				$('#info_address').each(function(idx){
					$(this).addClass('hidden');
					$('#form_address').removeClass('hidden');
				});
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
						name:"<?= $_SESSION['info']['name'] ?>",
						email:"<?= $_SESSION['info']['email'] ?>",
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

			$('input[name=pay_method]').prop('checked', false);

			$('input[name=pay_method]').change(function(){
				$('.pay_choice').addClass('hidden');
				$('#choice_'+$(this).val()).removeClass('hidden');
			});

			$('#cvv_pop').popover({
			  html: true,
			  trigger: 'hover',
			  content: function () {
			    return '<img src="/assets/img/nav/security_code.jpg" />';
			  }
			});

			$('#credit_no_input').keyup(function(e){
		        var $this = $(this);
		        if($this.val().length==19||!(e.keyCode>=48&&e.keyCode<=57||e.keyCode>=96&&e.keyCode<=105))return;
		        if ((($this.val().length+1) % 5)==0){
		            $this.val($this.val() + " ");
		        }
		    });  

			$('#block_agreement').click(function(){
				$('#block_refund_policy').removeClass('hidden');
			})

			$('#transfer_click').click(function(){
				$("#transfer_errors").html('');
				$('#form_transfer').validate({ errorPlacement: function(error, element) {}});
				if($('#form_transfer').valid()){
					$('#transfer_click').addClass('hidden');
					$('#transfer_processing').removeClass('hidden');
					$.ajax({
				        type: 'POST',
				        url:"/paid_confirm/",
				        data: new FormData($("#form_transfer")[0]),
				        processData: false, 
				        contentType: false,
				        dataType: 'json',
				        success: function(data) {
				        	if(!data.success&&(data.err_code=='ERR_SIZE'||data.err_code=='ERR_TYPE')){
				        		$("#transfer_errors").html(data.err_code=='ERR_SIZE'?'<strong>ไฟล์มีขนาดเกิน 5 MB</strong>':'<strong>อัพโหลดไฟล์ผิดประเภท</strong>');
				        		$('#transfer_errors_box').removeClass('hidden');
								$('#transfer_click').removeClass('hidden');
								$('#transfer_processing').addClass('hidden');
				        	}
				        	else{
				           		window.location = window.location;
				        	}
				        }
				    });
				}
			});
		</script>
		<script type="text/javascript">
			Omise.setPublicKey("pkey_test_5b0mgp0v548at8cij0c");

			$("#checkout").submit(function () {

			  var form = $(this);

			  form.find("input[type=submit]").prop("disabled", true);
			  form.find("input[type=submit]").addClass('hidden');
			  $('#credit_processing').removeClass('hidden');

			  var card = {
			    "name": form.find("[data-omise=holder_name]").val(),
			    "number": form.find("[data-omise=number]").val(),
			    "expiration_month": form.find("[data-omise=expiration_month]").val(),
			    "expiration_year": form.find("[data-omise=expiration_year]").val(),
			    "security_code": form.find("[data-omise=security_code]").val()
			  };

			  Omise.createToken("card", card, function (statusCode, response) {
			    if (response.object == "error" || !response.card.security_code_check) {
			      var message_text = "SECURITY CODE CHECK FAILED";
			      if(response.object == "error") {
			        message_text = response.message;
			      }
			      $('#token_errors_box').removeClass('hidden');
			      $("#token_errors").html(message_text);

			      form.find("input[type=submit]").prop("disabled", false);
			      form.find("input[type=submit]").removeClass('hidden');
			  	  $('#credit_processing').addClass('hidden');
			    } else {
			      form.find("[name=omise_token]").val(response.id);

			      form.find("[data-omise=number]").val("");
			      form.find("[data-omise=security_code]").val("");

			      form.get(0).submit();
			    };
			  });

			  return false;

			});
		</script>
	</body>
</html>