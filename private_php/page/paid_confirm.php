<?php
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	http_response_code(404);
	exit;
}

if($_FILES['transfer_pic']['size']>GEN::PIC_SIZE){
	die(json_encode([
		'success'=>false,
		'err_code'=>'ERR_SIZE'
	]));
}
$tmp_name = $_FILES['transfer_pic']['tmp_name'];
if(!preg_match(GEN::PIC_TYPE, mime_content_type($tmp_name))){
	die(json_encode([
		'success'=>false,
		'err_code'=>'ERR_TYPE'
	]));
}
$picture_file = $_POST['order_code'].'.'.pathinfo($_FILES['transfer_pic']['name'], PATHINFO_EXTENSION);
move_uploaded_file($tmp_name, dirname(__FILE__).'/../upload/paid_confirm/'.$picture_file);

$db = new Database();
$result = $db->call('chargeOrder', [$_POST['order_code'], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
if(!$result['success']||$result['count']<=0){
	die(json_encode([
		'success'=>false,
		'err_code'=>'ERR_SYS'
	]));
}
$total_price = json_decode($result['result'][0]['payment_detail'], true)['total_price'];
$payment_detail = json_encode([
	'address'	     =>$_POST['order_address'],
	'target_bank'    =>$_POST['target_bank'],
	'transfer_date'  =>$_POST['transfer_date'],
	'transfer_time'  =>$_POST['transfer_time'],
	'transfer_amount'=>$_POST['transfer_amount'],
	'total_price'    =>$total_price,
]);
$result = $db->call('billOrder', [
	$_POST['order_code'],
	($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null,
	$payment_detail,
	$picture_file
]);
if(!$result['success']||$result['result'][0][0]<=0){
	die(json_encode([
		'success'=>false,
		'err_code'=>'ERR_SYS'
	]));
}

die(json_encode([
	'success'=>true
]));
?>