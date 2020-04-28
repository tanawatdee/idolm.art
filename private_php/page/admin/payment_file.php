<?php

if($_SESSION['role']&ROLE::ADMIN){
	preg_match('/^\/api\/admin\/payment_file\/(.+)(\/([?].*)?)?$/', $request_uri, $match);
	$file = dirname(__FILE__).'/../../upload/paid_confirm/'.$match[1];
	$imginfo = getimagesize($file);
	header("Content-type: {$imginfo['mime']}");
	readfile($file);
}

if($_SESSION['role']&ROLE::USER){
	preg_match('/^\/api\/payment_file\/(.+)\/$/', $request_uri, $match);
	$db = new Database();
	$result = $db->call('getOrder', [$match[1], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
	if(!$result['success']||$result['count']<=0){
		die('มีข้อผิดพลาดเกิดขึ้น กรุณาติดต่อ Email: support@idolm.art');
	}

	$file = dirname(__FILE__).'/../../upload/paid_confirm/'.$result['result'][0]['payment_file'];
	$imginfo = getimagesize($file);
	header("Content-type: {$imginfo['mime']}");
	readfile($file);
}

?>