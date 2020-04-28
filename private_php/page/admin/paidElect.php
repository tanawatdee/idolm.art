<?php

include_once dirname(__FILE__).'/../../database/database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	switch ($_POST['action']) {
		case 'paidElection':
			$db = new Database();
			$result = $db->call('paidElection', [$_POST['id']]);
			$code_result = $result['result'][0];
			
			$code_from = $code_result['from'];
			$code_to = $code_from + $code_result['amount'];
			$code_directory = dirname(__FILE__).'/../../../private_html/vote/upload/code/';
			$code_list = [];
			for ($i_code = $code_from; $i_code < $code_to; $i_code++) {
				$code_list[] = $code_directory.'code_'.$i_code.'.jpg';
			}
			include_once dirname(__FILE__).'/../../api/mailer.php';
			Mail::sendCode($code_result['fbemail'], $code_result['fbname'], $code_list, date('d/m/y H:i', strtotime($code_result['order_time'])));
		break;
	}
}

$db = new Database();
$result = $db->call('listElection', []);
$elections = $result['result']?:[];

?>
<style type="text/css">
	img{
		max-height: 300px;
		max-width: 300px;
	}
</style>
<a href="/api/admin/dashboard/">หน้าหลักแอดมิน</a>
<h3>อนุมัติชำระเงิน</h3>
<hr>
<h3>รออนุมัติ</h3>
<table>
	<tr>
		<th>election_id</th>
		<th>customer_id</th>
		<th>หลักฐาน</th>
		<th>เวลาสั่ง</th>
		<th>จำนวน</th>
		<th>อนุมัติ</th>
	</tr>
	<?php foreach ($elections as $election):
		if($election['status'] != 'BILL') continue;
		$slip = json_decode($election['slip'], true);
		$file = dirname(__FILE__).'/../../../private_html/vote/upload/slip/'.$slip['picture_file'];
		$image = base64_encode(file_get_contents($file));
		$src = 'data: '.mime_content_type($file).';base64,'.$image;
	?>
		<tr>
			<form action="/api/admin/paidElect/" method="post">
				<td><?= $election['election_id'] ?></td>
				<td><?= $election['customer_id'] ?></td>
				<td><img src="<?= $src ?>"></td>
				<td><?= $election['order_time'] ?></td>
				<td><?= $election['amount'] ?></td>
				<td><button>อนุมัติ</button></td>
				<input type="hidden" name="action" value="paidElection">
				<input type="hidden" name="id" value="<?= $election['election_id'] ?>">
			</form>
		</tr>
	<?php endforeach; ?>
</table>
<h3>สถานะอื่น</h3>
<table>
	<tr>
		<th>election_id</th>
		<th>customer_id</th>
		<th>เวลาสั่ง</th>
		<th>จำนวน</th>
		<th>สถานะ</th>
		<th>เริ่มโค้ดที่</th>
	</tr>
	<?php foreach ($elections as $election):
		if($election['status'] == 'BILL') continue;
	?>
		<tr>
			<td><?= $election['election_id'] ?></td>
			<td><?= $election['customer_id'] ?></td>
			<td><?= $election['order_time'] ?></td>
			<td><?= $election['amount'] ?></td>
			<td><?= $election['status'] ?></td>
			<td><?= $election['from'] ?></td>
		</tr>
	<?php endforeach; ?>
</table>