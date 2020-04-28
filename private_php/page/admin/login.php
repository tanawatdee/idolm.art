<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'):
	include_once(dirname(__FILE__).'/../../database/database.php');

	$user = $_POST['username'];
	$pass = $_POST['password'];
	$db = new Database();
	$result = $db->call('getAdmin', [$user]);
	if(!$result['success']){
		die(json_encode([
			'success'=>false,
			'message'=>$result['message']
		]));
	}
	if($result['count']<=0){
		die(json_encode([
			'success'=>false,
			'message'=>'ERR_USR'
		]));
	}
	if(!password_verify($pass, $result['result'][0]['password_hash'])){
		die(json_encode([
			'success'=>false,
			'message'=>'ERR_PWD'
		]));
	}
	else{
		$_SESSION['role'] = ROLE::ADMIN;
		$_SESSION['info'] = ['username'=>$user];
			
		die(json_encode([
			'success'=>true
		]));
	}

else:

	if(isset($_GET['logout'])){
		$_SESSION['role'] = ROLE::GUEST;
		$_SESSION['info'] = null;
		header('Location: /api/admin/login/');
		die();
	}
	else if($_SESSION['role']&ROLE::ADMIN){
		header('Location: /api/admin/dashboard/');
		die();
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Admin Login</title>
	</head>
	<body>
		<form id="form_login" onsubmit="login(this);return false;">
			Username <input type="text" name="username">
			Password <input type="password" name="password">
			<input type="submit">
		</form>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script type="text/javascript">
		function login(elm){
			$.post( "/api/admin/login/", $(elm).serialize(), function(data){
				if(data.success){
					window.location = '/api/admin/dashboard/';
				}
				else if(data.message=='ERR_USR'){
					alert('User not found.');
					$('#form_login input[name=username]').focus();
				}
				else if(data.message=='ERR_PWD'){
					alert('Wrong password.');
					$('#form_login input[name=password]').focus();
				}
				else{
					alert('Database error.');
				}
			},'json');
		}

		$('#form_login input[name=username]').focus();
	</script>
	</body>
</html>
<?php endif; ?>