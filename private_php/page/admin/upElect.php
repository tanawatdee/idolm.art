<?php

$files = scandir(dirname(__FILE__).'/../../../private_html/vote/upload/code/');
$files[0] = null;
$files[1] = null;

$status = json_decode(implode('', file(dirname(__FILE__).'/../../../private_html/vote/status.json')), true);
$amount = $status['total_code'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	switch ($_POST['action']) {
		case 'upload':
			foreach ($_FILES['code']['tmp_name'] as $i => $tmp_name) {
				move_uploaded_file($tmp_name, dirname(__FILE__).'/../../../private_html/vote/upload/code/code_'.($amount+1).'.'.pathinfo($_FILES['code']['name'][$i], PATHINFO_EXTENSION));
				$amount++;
			}
			$status['total_code'] = $amount;
			$myfile = fopen(dirname(__FILE__).'/../../../private_html/vote/status.json', "w");
			fwrite($myfile, json_encode($status, JSON_PRETTY_PRINT));
			fclose($myfile);
			header("Location: /api/admin/upElect/");
		break;
	}
}

?>
<style type="text/css">
	.img_code{
		max-height: 50px;
		max-width: 100%;
	}
</style>
<a href="/api/admin/dashboard/">หน้าหลักแอดมิน</a>
<h3>ทั้งหมด <?= $amount ?> โค้ด</h3>
<form action="/api/admin/upElect/" method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="upload">
	<input type="file" name="code[]" multiple required>
	<input type="submit" value="อัพโหลด">
</form>
<ol>
	<?php foreach ($files as $file):
		if($file == null) continue;
		$file = dirname(__FILE__).'/../../../private_html/vote/upload/code/'.$file;
		$image = base64_encode(file_get_contents($file));
		$src = 'data: '.mime_content_type($file).';base64,'.$image;
	?>
		<li><img class="img_code" src="<?= $src ?>"></li>	
	<?php endforeach; ?>
</ol>