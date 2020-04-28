<?php

include_once('../private_php/config/constant.php');
include_once('../private_php/config/routemap.php');
include_once('../private_php/database/database.php');

session_start();

if(!isset($_SESSION['role']) || $_SESSION['time'] + SESS::TIME < time()){
	if(isset($_COOKIE['fblogin'])) {
		$cookie_key = hash('sha256', rand());

		$db = new Database();
		$result = $db->call('getCookie', [
			$_COOKIE['fblogin'],
			$cookie_key,
			SESS::C_TIME_STR
		]);

		if(!$result['success']||$result['count']<=0){
			$_SESSION['role'] = ROLE::GUEST;
			$_SESSION['info'] = null;
		}
		else{
			$result = $result['result'][0];
			$_SESSION['role'] = ROLE::USER;
			$_SESSION['info'] = [
				'id'   =>$result['fbid'],
				'name' =>$result['fbname'],
				'email'=>$result['fbemail']
			];
			setcookie('fblogin', $cookie_key, time() + SESS::C_TIME, '/');
		}
	}
	else{
		$_SESSION['role'] = ROLE::GUEST;
		$_SESSION['info'] = null;
	}
}
$_SESSION['time'] = time();

$session_role = $_SESSION['role'];
$request_uri = $_SERVER['REQUEST_URI'];

foreach(ROUTE::MAP as $routemap){
	preg_match('/^\/api\/'.$routemap[0].'(\/([?].*)?)?$/', $request_uri, $matches);
	if(count($matches)){
		$routefile  = $routemap[2];
		$is_hidden= !$routemap[3];
		$is_granted = $session_role&$routemap[1];
		break;
	}
}

if(!count($matches)){
	include('../private_php/error/404.php');
}
else if($is_granted){
	include('../private_php/'.$routefile);
}
else if($is_hidden){
	include('../private_php/error/403.php');
}
else{
	header('Location: /login?redirect='.urlencode($request_uri));
}

?>