<?php 

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'):

  include_once(dirname(__FILE__).'/../../../private_php/api/Facebook/autoload.php');
  include_once(dirname(__FILE__).'/../../../private_php/database/database.php');

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

  $_SESSION['role'] = true;
  $_SESSION['info'] = [
    'id'   =>$result['fbid'],
    'name' =>$result['fbname'],
    'email'=>$result['fbemail']
  ];
  $_SESSION['time'] = time();

  die(json_encode(true));

elseif(isset($_GET['logout'])):
  unset($_SESSION['role']);
  $_SESSION['info'] = null;
  $_SESSION['time'] = time();
  header('Location: /');
  exit;

else:
?>
<script type="text/javascript" src="//idolm.art/assets/js/jquery.js"></script>
<script type="text/javascript" src="//idolm.art/assets/js/fblogin.js"></script>
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
  $.post('/fblogin/', {token: access_token}, function(data){
    window.location.replace('/dashboard/');
  });
</script>
<?php endif; ?>