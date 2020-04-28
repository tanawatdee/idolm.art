<?php 

if($_SERVER['REQUEST_METHOD'] === 'POST'):

  include_once(dirname(__FILE__).'/../api/Facebook/autoload.php');

  $fb = new Facebook\Facebook([
    'app_id' => '',
    'app_secret' => '',
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

else:
?>
<script type="text/javascript" src="/assets/js/jquery.js"></script>
<script type="text/javascript" src="/assets/js/fblogin.js"></script>
<script type="text/javascript">
  hash = window.location.hash.split('#')[1].split('&').map(function(x){return x.split('=')});
  access_token = null;
  state = null;
  for(i=0;i<hash.length;i++){
    if(hash[i][0]=='access_token'){
      access_token = hash[i][1];
    }
    if(hash[i][0]=='state'){
      state = decodeURIComponent(hash[i][1]);
    }
  }
  $.post('/api/fblogin/', {token: access_token}, function(data){
    window.location.replace(state);
  });
</script>
<?php endif; ?>