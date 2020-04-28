<?php

$db = new Database();
$result = $db->call('accTime', [$_GET['from'], $_GET['to']]);
$result['result'] = $result['result']? : [];


$sum_amount = 0;
$sum_sales = 0;

if($_GET['action'] == 'file'){
	header("Content-type: text/csv; charset=utf-8");
	header("Content-Disposition: attachment; filename=account_time_{$_GET['from']}_{$_GET['to']}.csv");
	header("Pragma: no-cache");
	header("Expires: 0");
	echo "\xEF\xBB\xBF"; 
	$out = fopen('php://output', 'w');

	fputcsv($out, ['รหัสสินค้า', 'จำนวนที่ขาย', 'ยอดรวมที่ขาย', 'ชื่อสินค้า']);
	foreach ($result['result'] as $key => $val){
		fputcsv($out, [$val['product_code'], $val['amount'], $val['total_sales'], $val['product_name']]);
	}
	fclose($out);
	exit;
}

?>
<a href="/api/admin/dashboard/">หน้าหลักแอดมิน</a>
<h3>ทำบัญชี</h3>
<form action="/api/admin/account/">
	จากวันที่ <input type="date" name="from" value="<?=$_GET['from']?>"> ถึงวันที่ <input type="date" name="to" value="<?=$_GET['to']?>">
	<input type="submit" value="ตกลง">
</form>
<form action="/api/admin/account/" method="get">
	<input type="hidden" name="action" value="file">
	<input type="hidden" name="from" value="<?=$_GET['from']?>">
	<input type="hidden" name="to" value="<?=$_GET['to']?>">
	<input type="submit" value="โหลดไฟล์">
</form>
<table>
	<tr>
		<th>รหัสสินค้า</th>
		<th>ชื่อสินค้า</th>
		<th>จำนวนที่ขาย</th>
		<th>ยอดรวมที่ขาย</th>
	</tr>
	<?php foreach ($result['result'] as $key => $val):
		$sum_amount += $val['amount'];
		$sum_sales += $val['total_sales'];
	?>
	<tr>
		<td><?=$val['product_code']?></td>
		<td><?=$val['product_name']?></td>
		<td><?=$val['amount']?></td>
		<td><?=$val['total_sales']?></td>
	</tr>
	<?php endforeach; ?>
	<tr>
		<th><b>รวม</b></th>
		<td></td>
		<td><?=$sum_amount?></td>
		<td><?=$sum_sales?></td>
	</tr>
</table>