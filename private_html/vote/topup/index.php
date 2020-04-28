<?php 

session_start();

if(!isset($_SESSION['role']) || $_SESSION['time'] + 1800 < time()){
	unset($_SESSION['role']);
	$_SESSION['info'] = null;
	$_SESSION['time'] = time();
	header('Location: /');
	exit;
}
$_SESSION['time'] = time();

$bankStr = [
	'SCB'=>'ธ.ไทยพาณิชย์ 4072349531 รัชพล มาศผล',
	'KBA'=>'ธ.กสิกรไทย 0371082387 รัชพล มาศผล',
	'BUA'=>'ธ.กรุงเทพ 9390197441 รัชพล มาศผล',
	'KTB'=>'ธ.กรุงไทย 1620304821 รัชพล มาศผล',
	'TMB'=>'ธ.ทหารไทย 0792305799 รัชพล มาศผล',
	'TRU'=>'ทรูมันนี่ วอลเล็ท 0830245507 รัชพล มาศผล'
];

$status = json_decode(implode('', file(dirname(__FILE__).'/../status.json')), true);

include_once dirname(__FILE__).'/../../../private_php/database/database.php';
$db = new Database();
$amount = ($db->call('amountElection', [$status['total_code'], $_SESSION['info']['id']]))['result'][0][0];

$result = $db->call('getElection', [$_SESSION['info']['id']]);
if(!$result['success']){
	header('Location: /');
	exit;
}
$result['result'] = $result['result']? : [];

$bookAmount = null;
foreach ($result['result'] as $code) {
	if($code['status'] == 'BOOK'){
		$bookAmount = $code['amount'];
		break;
	}
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	switch ($_POST['action']) {
		case 'bookAmount':
			if($_POST['code_amount'] <= $amount && $_POST['code_amount'] > 0){
				$db = new Database();
				$db->call('newElection', [$_SESSION['info']['id'], $_POST['code_amount']]);
			}
			header('Location: /topup/');
			exit;
		break;
		case 'billElection':
			if($_FILES['transfer_pic']['size']>5242880){
				die(json_encode([
					'success'=>false,
					'err_code'=>'ERR_SIZE'
				]));
			}
			$tmp_name = $_FILES['transfer_pic']['tmp_name'];
			if(!preg_match('/^(image.*)$/', mime_content_type($tmp_name))){
				die(json_encode([
					'success'=>false,
					'err_code'=>'ERR_TYPE'
				]));
			}

			$picture_file = $_SESSION['info']['id'].(string)time().'.'.pathinfo($_FILES['transfer_pic']['name'], PATHINFO_EXTENSION);
			$realpath_file = dirname(__FILE__).'/../upload/slip/'.$picture_file;
			move_uploaded_file($tmp_name, $realpath_file);

			$post = $_POST;
			$post['picture_file'] = $picture_file;
			$post = json_encode($post);

			$db = new Database();
			$result =$db->call('billElection', [$_SESSION['info']['id'], $post]);
			$election_id = $result['result'][0]['election_id'];
			include_once dirname(__FILE__).'/../../../private_php/api/messenger.php';

			(new Messenger())->sendElection(
				$election_id,
				$bookAmount,
				(new Messenger())->uploadImage($realpath_file, mime_content_type($realpath_file))
			);

			die(json_encode(['success'=>true]));

		break;
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Vote for Oshi | Idolm.art</title>
	<link rel="shortcut icon" href="//idolm.art/assets/img/nav/logo_pink.png">
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css?family=Kanit:300" rel="stylesheet">
	<style type="text/css">
		html, body{
			padding: 0;
			margin: 0;
			height: 100%;
			font-family: 'Kanit', sans-serif;
			background-color: #25002e;
		}
		#viewport{
			min-height: 100%;
			min-width: 375px;
			max-width: 480px;
			width: 100%;
			background: linear-gradient(rgba(197, 142, 195, 1), rgba(197, 142, 195, 0.5));
    		background-color: white;
			margin: auto;
		}
		.btn-fb{
			background-color: #3b5998;
			border: none;
			height: 50px;
		}
		.img_code{
			max-height: 50px;
			max-width: 100%;
		}
		.fright{
			float: right;
		}
		.fbname{
			width: calc(100% - 120px);
			text-overflow: ellipsis;
			overflow: hidden;
			white-space: nowrap;
		}
		.selellips{
			text-overflow: ellipsis;
			overflow: hidden;
			white-space: nowrap;
		}
		.w140px{
			width: 140px;
		}
		.w200px{
			width: 200px;
		}
		.btn-success{
			background-color: #13CCB4;
			border: none;
		}
		.btn-success:hover{
			background-color: #1AB29F;
			border: none;
		}
		.btn-danger{
			background-color: #D2308C;
			border: none;
		}
		.badge-secondary{
			background-color: #925b9f85;
		}
		.hidden{
			display: none;
		}
	</style>
</head>
<body>
	<div id="viewport">
		<div class="container-fluid text-light">
			<div class="row">
				<div class="col-12">
					<div class="fright">
						<a href="/fblogin/?logout" target="_self" style="position: absolute; right: 15px; top: 24px;"><button class="btn btn-danger rounded-0">ออกจากระบบ</button></a>
					</div>
					<h3 class="fbname mt-3" style="color: black;"><img class="mb-2" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAO/SURBVGhD7VlbiE1RGF7rP9v9gaKkvHh2G8STcsk9SUouKZcQhnlxaaLRMIPIw7g+8DjJC6YRirzwIGPClMwQGTRG5MWYGUyJb+39O7NOZx+zzl5r7znprPpr7XX+//sv6/b/6whRbMUIFCNQjEAxAgUYgYlCyLWCaI9Pqi/EhAK0M8MkEoK2gZ4ImfodSoIe4/etkAJvQTVvMqLcmDZaUDtsvAW6yKT6H3t/l4+E8CYViAtyHozr9I0TsgH9FTBsWIhxGFO/wXifl76hP7e/ncBapy426ASMkQYGqaV2kmU6wT/eQCYWFg+GPNOMz1NJ2okmCAIr8Ubbedk0RlfNGz7Y2Ik2tQxecPQXR9dMSxmj2XD5RVeVKemVsOI2y+kfgEC0B1ge9lNiTW5iB67Zq6R6Xoob7LFMEShVwQ6cNhXJzUfnGGu/PZYpAqUOOnTgLGMdMFXvgE9uZqVX7cGojpfQRnssYwRvKjvwHiIpY7FsRnWXtPEmLrHAyVsURtMrjtz8vKXTAnIhB+IlhpJO8KiMHXhg4UADO1AaHSO65CDtMjucPwxVa5fYwPzlnUh40+BENxtyyBySqlimCxfYFHO5WDgl9gD94OV0D/1FUIMbNqshyoS0Q95n43+ivyAWk8xBaRmMup5VfQl6g/EroBomHLfUGsJ3G06sNNfnjFOuThcmQXHyFXQEVAZqyTL0b4kpCEkb7QBVgL6k+QQ9Bd56Z+b9A0id25c0xZ/BWw0aq8ngOPSmw6AtGKsMCBefP5ZxVI5mRz5oeHXgGRKjI3SZ128PlKvcZaQDZcOBtVur7G44wAyDkKvY+O+I6Bz3SrwZcKKDDwM1e04bal16zg7sdYqcAYbKLNhT7zDs8m5AdALgTwAeHJ8Dao/QW54FvHY4a7SPHah1BpkTiM6wrqMOdVEtg+KYjLvhOA1mu96hJrrDoEscguaASs3kJfTQoS68uAVRSaBqop2sq8WhA1SuXTZ4bY6r8VuTHyzhcg8ogzn9DWYCj7ah759RPVOp+SktSDVRgfqQo12aEkyxSuZsG6oyQU0abrktYh/ychYUNmsK7+J7OYQQReOmHrRwINBNDec1vlUqnkgbCmUoYJCF9maarfi+gDRjHRcpY2DJCCb0/f8Q1oDnPCiop4Pl2A06Dj7kRMk3lYVWZhjUa5hK+Dp8JwX1hNQCymG1Ucclb3a2RiwJORvDVTAKRYpf0ODtn34xoXREiiAIy00cA69KE+JMSaxjot6KVKqtZkjRKFB//Adg7UgR4L+PwB/k0vIZ9FIsQgAAAABJRU5ErkJggg=="> <?= htmlspecialchars($_SESSION['info']['name']) ?></h3>
					<hr>
					<h1 class="ml-3">ซื้อโค้ดเพิ่ม <span class="badge badge-secondary align-top">เหลือ <span class="badge badge-light"><?= $amount ?></span> โค้ด</span></h1>
				</div>
				<div class="col-12">
					<form id="form_amount" action="/topup/" method="post">
						<h5 class="mt-4 ml-3">1. ใส่จำนวนโค้ด (<?= $status['price'] ?> บาท / โค้ด)</h5>
						<?php if($bookAmount != null): ?>
						<h5 id="show_amount" class="ml-5"><span class="text-dark"><b><?= $bookAmount ?></b></span> โค้ด <button id="btn_amount" type="button" class="btn btn-dark btn-sm">แก้ไข</button></h5>
						<?php endif; ?>
	                    <div id="input_amount" class="input-group ml-5 w200px<?= $bookAmount != null?' hidden':'' ?>">
	                    	<input type="hidden" name="action" value="bookAmount">
	                      	<input type="number" class="form-control bg-light" name="code_amount" min="1" max="<?= $amount ?>" value="<?= $bookAmount ?>" required>
	                        <div class="input-group-append">
	                          <span class="input-group-text">โค้ด</span>
	                          <button class="btn btn-success">ตกลง</button>
	                        </div>
	                    </div>
					</form>

					<?php if($bookAmount != null): ?>
					<form id="form_transfer" onsubmit="transfer_submit();return false;">
	                  <input type="hidden" name="action" value="billElection">

	                  <h5 class="mt-4 ml-3">2. เลือกโอนเงินบัญชีใดบัญชีหนึ่งต่อไปนี้</h5>
	                  <table>
	                  	  <?php foreach($bankStr as $bank): ?>
	                      <tr>
	                      	<td>&emsp;&nbsp;•&nbsp;</td>
	                      	<td><?= substr($bank, 0, -42) ?>&nbsp;</td>
	                      	<td><?= substr($bank, -43, 11) ?>&nbsp;</td>
	                      	<td><?= substr($bank, -32, 32) ?></td>
	                      </tr>
	                  	  <?php endforeach; ?>
	                  </table>
	                  <h5 class="mt-4 ml-3">3. โอนเงินจำนวน <span class="text-dark"><b><?= number_format($status['price']*$bookAmount) ?></b></span> บาท</h5>
	                  <h5 class="mt-4 ml-3 mb-3">4. ส่งหลักฐานการโอนเงินภายใน<br>&emsp;<span class="text-dark"><b><?= date("วันที่ d/m/y เวลา H:i น.", strtotime($code['order_time']) + 7200); ?></b></span></h5>
	                  <div class="input-group">
	                      <div class="input-group-prepend">
	                        <span class="input-group-text w140px">ธนาคารปลายทาง</span>
	                      </div>
	                    <select class="form-control bg-light selellips" name="target_bank" required>
	                    	  <?php foreach($bankStr as $code => $bank): ?>
	                          <option value="<?= $code ?>"><?= $bank ?></option>
	                          <?php endforeach; ?>
	                    </select>
	                  </div>
	                  <div class="input-group">
	                      <div class="input-group-prepend">
	                        <span class="input-group-text w140px">วันที่โอน</span>
	                      </div>
	                    <input type="date" class="form-control bg-light" name="transfer_date" required>
	                  </div>
	                  <div class="input-group">
	                      <div class="input-group-prepend">
	                        <span class="input-group-text w140px">เวลาที่โอน</span>
	                      </div>
	                    <input type="time" class="form-control bg-light" name="transfer_time" required>
	                  </div>
	                  <div class="input-group">
	                      <div class="input-group-prepend">
	                        <span class="input-group-text w140px">จำนวนเงิน (บาท)</span>
	                      </div>
	                    <input type="number" step="0.01" class="form-control bg-light" name="transfer_amount" value="<?= $status['price']*$bookAmount ?>.00" required>
	                  </div>
	                  <div class="input-group">
	                      <div class="input-group-prepend">
	                        <span class="input-group-text w140px">หลักฐานการโอน</span>
	                      </div>
	                      <div class="custom-file">
	                        <input type="file" class="custom-file-input bg-light" id="transfer_pic" name="transfer_pic" accept="image/*" required>
	                        <label class="custom-file-label text-truncate" for="transfer_pic" id="lbl_transfer_pic">ไฟล์ภาพ</label>
	                      </div>
	                  </div>
	                  <div class="alert alert-danger mt-2 hidden" role="alert" id="transfer_errors_box">
	                    <strong><span id="transfer_errors">error</span></strong>
	                  </div>
	                  <div class="row">
	                    <div class="col mt-2 text-center">
	                      <button class="btn btn-success rounded-0" id="transfer_click">ส่งหลักฐาน</button>
	                      <button class="btn btn-info rounded-0 hidden" id="transfer_processing" disabled>กำลังดำเนินการ...</button>
	                      <a href="/dashboard/" target="_self"><button type="button" class="btn btn-danger rounded-0">กลับไปที่โค้ดของคุณ</button></a>
	                    </div>
	                  </div>
	                </form>
	            	<?php endif; ?>
				</div>
				<div class="col-12 text-center">
					<a href="//idolm.art/" target="_blank"><img class="my-4" src="data:image/png;base64,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" height="26"></a>
				</div>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script type="text/javascript">
		document.getElementById('transfer_pic').onchange = function(){
			document.getElementById('lbl_transfer_pic').innerHTML = document.getElementById('transfer_pic').value.replace(/.*(\/|\\)/, '');
		};

		$('#btn_amount').click(function(){
			$('#input_amount').removeClass('hidden');
			$('#show_amount').addClass('hidden');
		});

		function transfer_submit(){
			$("#transfer_errors").html('');
			$('#transfer_errors_box').addClass('hidden');
			$('#transfer_click').addClass('hidden');
			$('#transfer_processing').removeClass('hidden');
			$.ajax({
		        type: 'POST',
		        url:"/topup/",
		        data: new FormData($("#form_transfer")[0]),
		        processData: false, 
		        contentType: false,
		        dataType: 'json',
		        success: function(data) {
		        	if(!data.success&&(data.err_code=='ERR_SIZE'||data.err_code=='ERR_TYPE')){
		        		$("#transfer_errors").html(data.err_code=='ERR_SIZE'?'<strong>ไฟล์มีขนาดเกิน 5 MB</strong>':'<strong>อัพโหลดไฟล์ผิดประเภท</strong>');
		        		$('#transfer_errors_box').removeClass('hidden');
						$('#transfer_click').removeClass('hidden');
						$('#transfer_processing').addClass('hidden');
		        	}
		        	else{
		           		location.replace('/dashboard/');
		        	}
		        }
		    });
			return false;
		}
	</script>
</body>
</html>