<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){

  include_once(dirname(__FILE__).'/../api/Facebook/autoload.php');

  $fb = new Facebook\Facebook([
    'app_id' => '2043330669254065',
    'app_secret' => 'c447961cdc92b7e1f47cf94ef610a060',
    'default_graph_version' => 'v2.12',
    ]);

  try {
    $response = $fb->get('/me?fields=id,name,email', $_POST['token']);
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

  $user = $response->getGraphUser();

  $db = new Database();
  $result = $db->call('loginFB', [
    (string)$user['id'],
    (string)$user['name'],
    (string)$user['email']
  ]);

  if(!$result['success']||$result['count']<=0){
    die(json_encode(false));
  }
  $result = $result['result'][0];

  $_SESSION['role'] = ROLE::USER;
  $_SESSION['info'] = [
    'id'   =>$result['fbid'],
    'name' =>$result['fbname'],
    'email'=>$result['fbemail']
  ];
  $cookie_key = hash('sha256', rand());
  $result = $db->call('newCookie', [$cookie_key, $_SESSION['info']['id'], SESS::C_TIME_STR]);
  if(!$result['success']){
    die(json_encode(false));
  }
  setcookie('fblogin', $cookie_key, time() + SESS::C_TIME, '/');

  die(json_encode(true));
}

else if(isset($_GET['logout'])){
  if(isset($_COOKIE['fblogin'])) {
    $db = new Database();
    $db->call('delCookie', [$_COOKIE['fblogin']]);
  }

  $_SESSION['role'] = ROLE::GUEST;
  $_SESSION['info'] = null;
  setcookie('fblogin', '', time() - 3600, '/');
  die(json_encode(true));
}

else if($_SESSION['role']&ROLE::USER){
  header('Location: /');
  die();
}

$redirect = isset($_GET['redirect'])?$_GET['redirect']:'/';

?>


<!DOCTYPE html>
<html>
  <head>
    <?php $head_title = 'Login'; ?>
    <?php include(dirname(__FILE__).'/common/header.php'); ?>
  </head>
  <body class="bg-secondary">
    <?php include(dirname(__FILE__).'/common/navbar.php'); ?>
    <div class="container-fluid h-100">
      <div class="row h-100">
        <div class="col"></div>
        <div class="col-auto">
          <div class="h-50" style="display: inline-block;"></div>
          <button type="button" class="btn btn-primary loginFB_click" style="display: inline-block;vertical-align: middle;box-shadow: 0 1px 8px rgba(.5,.5,.5,.5)">
            <img src="/assets/img/nav/fb_white.png" height="28"> ล็อกอินเพื่อดูหน้านี้
          </button>
        </div>
        <div class="col"></div>
      </div>
    </div>
    <?php include(dirname(__FILE__).'/common/script.php'); ?>
    <script type="text/javascript">
      $('.loginFB_click').unbind('click');
      $('.loginFB_click').click(function(){
        loginFB('<?= $redirect ?>');
      });
    </script>
  </body>
</html>