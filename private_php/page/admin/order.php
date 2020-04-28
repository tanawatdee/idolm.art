<?php 

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	if($_POST['action']=='paid_confirm'){
		$db = new Database();
		$result = $db->call('setStatOrder', [$_POST['order_code'], 'PAID', $_SESSION['info']['username']]);
		die();
	}
	if($_POST['action']=='pack_confirm'){
		$db = new Database();
		$result = $db->call('setStatOrder', [$_POST['order_code'], 'PACK', $_SESSION['info']['username']]);
		die();
	}
	if($_POST['action']=='sent_confirm'){

		$db = new Database();
		$result = $db->call('sentOrder', [$_POST['order_code'], $_POST['track_no'], $_SESSION['info']['username']]);
		$tracks = explode(' ', $_POST['track_no']);
		$orders = explode(' ', $_POST['order_code']);
		$fbids = explode(' ', $_POST['fb_id']);

		include_once(dirname(__FILE__).'/../../api/mailer.php');
		foreach ($tracks as $i => $track) {
			$result = $db->call('getFB', [$fbids[$i]]);
			Mail::sendTrack($result['result'][0]['fbemail'], $result['result'][0]['fbname'], $orders[$i], $track);
		}
		die();
	}
}

$_GET['status'] = isset($_GET['status'])?$_GET['status']:'BOOK';
$get_status = $_GET['status'];
$db = new Database();
$result = $db->call('adminListOrder', [$_GET['status']]);
if(!$result['success']){
	die('db Error: '.$result['message']);
}
$orderList = $result['count']?$result['result']:[];

$order_idx = 0;
if(isset($_GET['skip_order'])){
	for($order_idx = 0; isset($orderList[$order_idx])&&$orderList[$order_idx]['order_code']!=$_GET['skip_order'];$order_idx++);
	$order_idx++;
}
else if(isset($_GET['select_order'])){
	for($order_idx = 0; isset($orderList[$order_idx])&&$orderList[$order_idx]['order_code']!=$_GET['select_order'];$order_idx++);
}
?>
<html>
	<head>
		<style type="text/css">
			.hidden{
				display:none;
			}
		</style>
	</head>
	<body>
		<a href="/api/admin/dashboard/">หน้าหลักแอดมิน</a><br>
		<a href="/api/admin/order/?status=BOOK" title="สินค้าที่ติดจองอยู่ในใบสั่งซื้อ">BOOK</a> 
		<a href="/api/admin/order/?status=BILL" title="สินค้าที่ลูกค้าแจ้งโอนเงินแล้ว รอแอดมินยืนยัน">BILL</a> 
		<a href="/api/admin/order/?status=PAID" title="สินค้าที่จ่ายเงินแล้ว รอแอดพิมพ์ใบแพ็กของ">PAID</a> 
		<a href="/api/admin/order/?status=PRINT" title="ปริ้นแล้ว รอแพ็กของ">PRINT</a> 
		<a href="/api/admin/order/?status=PACK" title="แพ็กของแล้ว รอส่งของ">PACK</a> 
		<a href="/api/admin/order/?status=SENT" title="ส่งของแล้ว ลงเลขแทร็คให้ลูกค้าแล้ว">SENT</a> 
		<a href="/api/admin/order/?status=FAIL" title="ออเดอร์ถูกยกเลิก">FAIL</a>
		<a href="/api/admin/product/">รายการสินค้า</a>
		<h3>รายการออเดอร์ในสถานะ <?= $_GET['status'] ?></h3>
		<?php if($get_status=='BILL'&&isset($orderList[$order_idx])): 
			$order = $orderList[$order_idx];
			$path = dirname(__FILE__).'/../../upload/paid_confirm/'.$order['payment_file'];
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$order['payment_detail'] = json_decode($order['payment_detail'],true);
		?>
			<div style="display: inline-block;max-width: 500px;">
				<table>
				<tr><td>ชื่อ</td><td><?= $order['fbname'] ?></td></tr>
				<tr><td>รหัส</td><td><?= $order['order_code'] ?></td></tr>
				<tr><td>วิธีส่ง</td><td><?= $order['delivery_type'] ?></td></tr>
				<tr><td>ค่าส่ง</td><td><?= $order['delivery_fee'] ?></td></tr>
				<tr><td>ที่อยู่</td><td><?= $order['payment_detail']['address'] ?></td></tr>
				<tr><td>ธนาคาร</td><td><?= $order['payment_detail']['target_bank'] ?></td></tr>
				<tr><td>วัน</td><td><?= $order['payment_detail']['transfer_date'] ?></td></tr>
				<tr><td>เวลา</td><td><?= $order['payment_detail']['transfer_time'] ?></td></tr>
				<tr><td>โอนมา</td><td><?= $order['payment_detail']['transfer_amount'] ?></td></tr>
				<tr><td>ราคา</td><td><?= $order['payment_detail']['total_price'] ?></td></tr>
				</table>
				<div>
					<button data-code="<?= $order['order_code'] ?>" onclick="paid_confirm(this);">อนุมัติ</button><button onclick="window.location='/api/admin/order/?status=BILL&skip_order=<?= $order['order_code'] ?>';">ข้าม</button>
				</div>
			</div>
			<div style="height: 350px; overflow-y: auto; display: inline-block;"><img src="<?=$base64?>"></div>
		<?php endif; ?>
		<?php if($get_status=='PAID'):
			$db = new Database();
			$result = $db->call('printOrder', [$_SESSION['info']['username']]);
			$printOrderList = $result['count']>0?$result['result']:[];
		?>
			<button onclick="printAll();">พิมพ์ทั้งหมด</button>
		<?php elseif($get_status=='PRINT'): ?>
			<button onclick="packAll();">แพ็กทั้งหมด</button>
		<?php endif; ?>
		<form id="form_order" onsubmit="return false;">
		<table>
				<tr>
					<th>
						<?php if($get_status=='PRINT'): ?>
							<button onclick="pack_confirm();">แพ็ก</button>
							<button onclick="printCustom();">พิมพ์</button>
						<?php elseif($get_status=='PAID'): ?>
							<button onclick="printCustom();">พิมพ์</button>
						<?php endif; ?>
					</th>
					<th>รหัสออเดอร์</th>
					<th>ชื่อลูกค้า</th>
					<th>สถานะ</th>
					<th>วิธีส่ง</th>
					<th>วิธีชำระ</th>
					<th>ราคารวม</th>
					<?php if(in_array($get_status, ['PACK','SENT'])): ?>
					<th>
						<button onclick="sendTrack();">ส่งเลขแทร็ก</button>
					</th>
					<?php endif; ?>
					<?php if($get_status=='SENT'): ?>
					<th>เลขแทร็ก</th>
					<?php endif; ?>
					<th>สินค้า</th>
					<th>ที่อยู่จัดส่ง</th>
				</tr>
			<?php foreach($orderList as $ti => $order): 
				$payment_detail = json_decode($order['payment_detail'],true);
			?>
				<tr>
					<td style="text-align: right;"><input type="checkbox" name="chk_order[]" value="<?= $order['order_code'] ?>"></td>
					<td><a href="/api/admin/order/?status=<?= $order['status'] ?>&select_order=<?= $order['order_code'] ?>"><?= $order['order_code'] ?></a></td>
					<td><a href="https://facebook.com/<?= $order['fbid'] ?>" target="_blank"><?= $order['fbname']?:$payment_detail['name'] ?></a></td>
					<td><?= $order['status'] ?></td>
					<td><?= $order['delivery_type'] ?></td>
					<td><?= null !==$payment_detail['target_bank']?'<a href="/api/admin/payment_file/'.$order['payment_file'].'" target="_blank">'.$payment_detail['target_bank'].'</a>':(null !== $payment_detail['charge_id']?'<a href="https://dashboard.omise.co/test/charges/'.$payment_detail['charge_id'].'" target="_blank">OMISE</a>':'-') ?></td>
					<td><?= $payment_detail['total_price'] ?></td>
					<?php if(in_array($get_status, ['PACK','SENT'])): ?>
					<td>
						<input type="text" autocomplete="off" name="post_track[]" tabindex="<?=$ti+1?>" data-code="<?= $order['order_code'] ?>" data-fbid="<?= $order['fbid'] ?>" onkeyup="validateTrack(this);">
					</td>
					<?php endif; ?>
					<?php if($get_status=='SENT'): ?>
					<td><?= $order['tracking_no'] ?></td>
					<?php endif; ?>
					<td><?= $order['product_code'] ?></td>
					<td><?= $order['status']=='BOOK'?$order['address']:($payment_detail['address']?:$payment_detail['address_raw']['name_tel'].' '.$payment_detail['address_raw']['address'].' '.$payment_detail['address_raw']['post']) ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		</form>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script type="text/javascript">
			function paid_confirm(elm){
				$.post('/api/admin/order/', {action:'paid_confirm', order_code:"<?= $orderList[$order_idx]['order_code'] ?>"}, function(){
					window.location = "/api/admin/order/?status=BILL<?= isset($orderList[$order_idx+1])?'&select_order='.$orderList[$order_idx+1]['order_code']:'' ?>";
				});
			}

			function pack_confirm(){
				order_code = ($('#form_order').serializeArray()).map(function(elm){
					return elm.value;
				}).join(',');

				$.post('/api/admin/order/', {action:'pack_confirm', order_code: order_code}, function(){
					window.location = window.location;
				});
			}

			function packAll(){
				$('#form_order').find('input[name="chk_order[]"]').attr('checked', true);
				pack_confirm();
			}

			function printAll(){
				win_print = window.open('/api/admin/print_order', '_blank');
				timer = setInterval(function() {   
				    if(win_print.closed) {
				        clearInterval(timer);  
				        window.location = window.location; 
				    }
				}, 1000);
			}

			function printCustom(){
				window.open('/api/admin/print_order/?'+$('#form_order').serialize(), '_blank');
			}

			function sendTrack(){
				order_code = [];
				track_no = [];
				fb_id = [];
				$('input[name="post_track[]"]').each(function(){
					track_val = $(this).val().trim();
					if(track_val!=''){
						order_code.push($(this).attr('data-code'));
						track_no.push(track_val);
						fb_id.push($(this).attr('data-fbid'));
					}
				});
				order_code = order_code.join(' ');
				track_no = track_no.join(' ');
				fb_id = fb_id.join(' ');
				$.post('/api/admin/order/', {action:'sent_confirm', order_code:order_code, track_no:track_no, fb_id:fb_id}, function(d){
					window.location = window.location;
				});
			}

			function validateTrack(elm){
				elm = $(elm);
				val = elm.val();
				if(val.indexOf(' ')!=-1){
					elm.css('background-color', 'rgb(255, 151, 151)');
					alert('ห้ามมีช่องว่าง');
					return;
				}
				if(val.slice(-2) != 'TH'){
					elm.css('background-color', 'white');
					return;
				}
				elm.css('background-color', checkSum(val.slice(2, -2))? '#97ff97' : 'rgb(255, 151, 151)');
			}

			function checkSum(strBarCode){
	            strBarCode = 'XX' + strBarCode + 'XX';
	            SumAll = 0;
	            SumAll = SumAll + (parseInt(strBarCode.substring(3, 2)) * 8);
	            SumAll = SumAll + (parseInt(strBarCode.substring(4, 3)) * 6);
	            SumAll = SumAll + (parseInt(strBarCode.substring(5, 4)) * 4);
	            SumAll = SumAll + (parseInt(strBarCode.substring(6, 5)) * 2);
	            SumAll = SumAll + (parseInt(strBarCode.substring(7, 6)) * 3);
	            SumAll = SumAll + (parseInt(strBarCode.substring(8, 7)) * 5);
	            SumAll = SumAll + (parseInt(strBarCode.substring(9, 8)) * 9);
	            SumAll = SumAll + (parseInt(strBarCode.substring(10, 9)) * 7);
	            Result = SumAll % 11;
	            if (Result == 0) {
	                if (parseInt(strBarCode.substring(11, 10)) == 5) {
	                    return true;
	                } else {
	                    return false;
	                }
	            } else if (Result == 1) {
	                if (parseInt(strBarCode.substring(11, 10)) == 0) {
	                    return true;
	                } else {
	                    return false;
	                }
	            } else if (parseInt(strBarCode.substring(11, 10)) == (11 - Result)) {
	                return true;
	            } else {
	                return false;
	            }
	        }
		</script>
	</body>
</html>