<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$action = $_POST['action'];
	switch ($action) {
		case 'price':
			$code = $_POST['code'];
			$price = $_POST['price'];
			$user = $_SESSION['info']['username'];

			$db = new Database();
			$result = $db->call('editPrice', [$code, $price, $user]);
		break;
		case 'amount':
			$code = $_POST['code'];
			$add_amount = $_POST['amount'];
			$user = $_SESSION['info']['username'];

			$db = new Database();
			$result = $db->call('editAmount', [$code, $add_amount, $user]);
		break;
		case 'recomended':
			$recomended = $_POST['recomended'];
			$landing = json_decode(implode('', file(dirname(__FILE__).'/../../config/landing.json')), true);
			$landing['recommended'] = explode(' ', $recomended);
			$myfile = fopen(dirname(__FILE__).'/../../config/landing.json', "w");
			fwrite($myfile, json_encode($landing, JSON_PRETTY_PRINT));
			fclose($myfile);
		break;
	}
}

$db = new Database();
$result = $db->call('listProduct', []);
if(!$result['success']){
	die('db Error: '.$result['message']);
}
$productList = $result['count']>0?$result['result']:[];

$landing = json_decode(implode('', file(dirname(__FILE__).'/../../config/landing.json')), true);

?>
<a href="/api/admin/dashboard/">หน้าหลักแอดมิน</a>
<h3>แก้ข้อมูล</h3>
<table>
	<tr>
		<form action="/api/admin/edit/" method="post">
			<input type="hidden" name="action" value="price">
			<td>
				แก้ราคา
			</td>
			<td>
				รหัสสินค้า
			</td>
			<td>
				<select name="code">
					<?php foreach($productList as $product): ?>
						<option value="<?= $product['product_code'] ?>"><?= $product['product_code'] ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<td>
				ราคาใหม่
			</td>
			<td>
				<input type="number" name="price">
			</td>
			<td>
				<input type="submit" value="แก้">
			</td>
		</form>
	</tr>
	<tr>
		<form action="/api/admin/edit/" method="post">
			<input type="hidden" name="action" value="amount">
			<td>
				แก้จำนวน
			</td>
			<td>
				รหัสสินค้า
			</td>
			<td>
				<select name="code">
					<?php foreach($productList as $product): ?>
						<option value="<?= $product['product_code'] ?>"><?= $product['product_code'] ?></option>
					<?php endforeach; ?>
				</select>
			</td>
			<td>
				เพิ่ม(ติดลบได้)
			</td>
			<td>
				<input type="number" name="amount">
			</td>
			<td>
				<input type="submit" value="แก้">
			</td>
		</form>
	</tr>
	<tr>
		<form action="/api/admin/edit/" method="post">
			<input type="hidden" name="action" value="recomended">
			<td>
				ของมันต้องมี
			</td>
			<td colspan="4">
				<textarea name="recomended" style="width: 100%;"><?= implode(' ', $landing['recommended']) ?></textarea>
			</td>
			<td>
				<input type="submit" value="แก้">
			</td>
		</form>
	</tr>
</table>