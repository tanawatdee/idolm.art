<?php

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
	http_response_code(404);
	exit;
}
require_once(dirname(__FILE__).'/../api/omise-php-2.9.1/lib/Omise.php');

define('OMISE_PUBLIC_KEY', GEN::OMISE_PKEY);
define('OMISE_SECRET_KEY', GEN::OMISE_SKEY);

$db = new Database();
$result= $db->call('chargeOrder', [$_POST['order_code'], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
if(!$result['success']||$result['count']<=0){
	header('Location: /order/'.$_POST['order_code'].'/?error='.GEN::ERR_STR['SYS']);
	die();
}
$total_price = json_decode($result['result'][0]['payment_detail'], true)['total_price'];

$card_token = $_POST['omise_token'];
$charge_amount_baht = $total_price;
$metadata = $_SESSION['info'];
$metadata['order_code'] = $_POST['order_code'];

$charge = OmiseCharge::create(array(
  'amount' => $charge_amount_baht*100,
  'currency' => 'thb',
  'card' => $card_token,
  'metadata' => $metadata
));

if($charge['status']!='successful'){
	switch ($charge['failure_code']) {
		case 'invalid_security_code ': $error_message = 'หมายเลขหลังบัตรไม่ถูกต้อง'; break;
		case 'payment_rejected': $error_message = 'ผู้ถือบัตรกรอกรหัส OTP ไม่ถูกต้อง'; break;
		case 'insufficient_fund': $error_message = 'วงเงินบัตรไม่เพียงพอ'; break;
		case 'stolen_or_lost_card': $error_message = 'บัตรใบนี้เป็นบัตรหายหรือถูกโจรกรรม'; break;
		case 'failed_processing': $error_message = 'การดำเนินการไม่สำเร็จ'; break;
		case 'payment_rejected': $error_message = 'ธุรกรรมถูกปฏิเสธ'; break;
		case 'failed_fraud_check': $error_message = 'บัตรไม่ผ่านการตรวจสอบของระบบคัดกรองการทุจริต'; break;
		case 'invalid_account_number': $error_message = 'หมายเลขบัญชีไม่ถูกต้อง'; break;
		default: $error_message = 'การ Charge ไม่สำเร็จ'; break;
	}
	header('Location: /order/'.$_POST['order_code'].'/?error='.$error_message);
	die();
}

$payment_detail = json_encode([
	'address'	 =>$_POST['order_address'],
	'total_price'=>$total_price,
	'charge_id'  =>$charge['id']
]);

$db = new Database();
$result= $db->call('paidOrder', [
	$_POST['order_code'],
	($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null,
	$payment_detail
]);
if(!$result['success']){
	header('Location: /order/'.$_POST['order_code'].'/?error='.GEN::ERR_STR['SYS']);
	die();
}
header('Location: /order/'.$_POST['order_code'].'/');
die();
?>