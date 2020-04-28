<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	if($_POST['action']=='printSetOrder'){
		$db = new Database();
		$result = $db->call('printSetOrder', [$_POST['orderListStr'], $_SESSION['info']['username']]);
		die();
	}
}

$db = new Database();

$result = $db->call('printOrder', [$_SESSION['info']['username'], isset($_GET['chk_order'])?implode(',',$_GET['chk_order']):null]);

if($result['count']<=0){
	die('nothing to be printed');
}
$addressList = [];
$orderListStr = [];

foreach($result['result'] as $order){
	$address = json_decode(json_decode($order['payment_detail'], true)['address']?:json_encode(json_decode($order['payment_detail'], true)['address_raw']), true);
	$isBangkok = $address['post'][0]=='1'&&$address['post'][1]=='0';
	$address = '<hr style="border-top: dashed 1px;">จาก&nbsp;&nbsp;&nbsp;&nbsp;&emsp;<img style="margin-bottom: 10px; vertical-align: top;" src="data:image/png;base64,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"><br>&nbsp;&nbsp;&nbsp;660/45 ไอดีโอคิวสามย่าน ถนนพระรามสี่ แขวงมหา-<br>&nbsp;&nbsp;พฤฒาราม เขตบางรัก กทม. 10500 โทร. 083 0245507<br><hr style="border-top: dashed 1px;">กรุณาส่ง&nbsp;&nbsp;&nbsp;'
	.($address['name_tel']?:$address['name'].' '.$address['tel']).'<br>'
	.($address['address']?:$address['home'].' '.$address['place'].($isBangkok?' แขวง':' ต.').$address['subdistrict'].($isBangkok?' เขต':' อ.').$address['district'].($isBangkok?' ':' จ.').$address['province'])
	.'<br>&emsp;&emsp;รหัสไปรษณีย์<div class="post_code">&nbsp;&nbsp;<div class="post_block">'.implode('</div><div class="post_block">',str_split(trim($address['post']))).'</div></div><br><hr>';
	for($i=0; $i < ($order['delivery_fee'] / GEN::DELIVERY[$order['delivery_type']]) || $i < 1; $i++){
		$addressList[] = '<div>'.$address.$order['order_str'].'</div>';	
	}
	$orderListStr[] = $order['order_code'];
}

?>
<style type="text/css">
	html{
		margin-top: 0;
	}
	body{
		margin-top: 0;
		text-align: center;
	}
	.main_block{
		height: 28cm;
		width: 9.5cm;
		margin: 0.1cm;
		margin-top: 0;
		margin-left: 0;
		margin-right: 0;
		padding: 0.1cm;
		border-left: solid 1px;
		border-right: solid 1px;
		display: inline-block;
		overflow: hidden;
		text-align: left;
	}
	.main_block > div{
		min-height: 4cm;
		width: 9.5cm;
		word-wrap: break-word;
	}
	.post_code{
		display: inline-block;
		font-size: 1.5em;
		font-weight: bold;
	}
	.post_block{
		display: inline-block;
		width: 0.7cm;
		height: 0.7cm;
		margin: 3px;
		text-align: center;
		border: solid 2px black;
	}
</style>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript">
	arr = <?= json_encode($addressList) ?>;
	orderListStr = "<?= implode(',', $orderListStr) ?>";
	window.onload = function(){
		$('body').append('<div class="main_block"></div>');
		for(i=0;i<arr.length;i++){
			$('.main_block').last().append(arr[i]);
			if((i!=0)&&!(i%3)){//$(".main_block").last().prop('scrollHeight') - $(".main_block").last().height() > 10){
				$(".main_block").last().children('div').last().remove();
				$('body').append('<div class="main_block"></div>');
				$('.main_block').last().append(arr[i]);
			}
		}
		window.print();
		setTimeout(function(){
		   $('body').html('<button onclick="markPrinted();">พิมพ์แล้ว</button>');
		}, 1000);
	};

	function markPrinted(){
		$.post('/api/admin/print_order/', {action: 'printSetOrder', orderListStr: orderListStr}, function(){
			window.close();
		});
	}
</script>