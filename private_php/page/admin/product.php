<?php 

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	if($_POST['action']=='newProduct'){
		$product_code = $_POST['product_code'];
		$amount = $_POST['amount'];
		$hide_amount = $_POST['hide_amount'];
		$price = $_POST['price'];
		$product_name = $_POST['product_name'];
		$product_description = $_POST['product_description'];
		$tag_name = $_POST['tag_name'];
		$picture_file = [];
		foreach ($_FILES['picture_file']['tmp_name'] as $i => $tmp_name) {
			$picture_file[] = $_POST['product_code'].'_'.($i).'.'.pathinfo($_FILES['picture_file']['name'][$i], PATHINFO_EXTENSION);
			move_uploaded_file($tmp_name, dirname(__FILE__).'/../../../private_html/assets/img/product/'.end($picture_file));
		}

		include_once(dirname(__FILE__).'/../../api/image_resize.php');

		foreach ($picture_file as $pic) {
			$pic = dirname(__FILE__).'/../../../private_html/assets/img/product/'.$pic;
			$pic_mime = mime_content_type($pic);
			$im = $pic_mime=='image/png'?imagecreatefrompng($pic):imagecreatefromjpeg($pic);
			$pic_w = imagesx($im);
			$pic_h = imagesy($im);
			$watermark = dirname(__FILE__).'/../../upload/template/watermark.png';
			$watermark_tmp = dirname(__FILE__).'/../../upload/template/watermark_tmp.png';
			smart_resize_image($watermark , null, $pic_w<$pic_h?$pic_w:$pic_h , $pic_w<$pic_h?$pic_w:$pic_h , false , $watermark_tmp , false , false ,100 );
			$watermark_tmp = imagecreatefrompng($watermark_tmp);
			//imagecopy($im, $watermark_tmp, $pic_w<$pic_h?0:($pic_w-$pic_h)/2, $pic_w<$pic_h?($pic_h-$pic_w)/2:0, 0, 0, imagesx($watermark_tmp), imagesy($watermark_tmp));
			$im = $pic_mime=='image/png'?imagepng($im, $pic):imagejpeg($im, $pic);
		}

		foreach ($picture_file as $pic) {
			$spic = dirname(__FILE__).'/../../../private_html/assets/img/product/s_'.$pic;
			$pic = dirname(__FILE__).'/../../../private_html/assets/img/product/'.$pic;
			$pic_mime = mime_content_type($pic);
			$im = $pic_mime=='image/png'?imagecreatefrompng($pic):imagecreatefromjpeg($pic);
			$pic_w = imagesx($im);
			$pic_h = imagesy($im);
			smart_resize_image($pic , null, $pic_w<$pic_h?230:$pic_w/$pic_h*230 , $pic_w<$pic_h?$pic_h/$pic_w*230:230 , false , $spic , false , false ,100 );
			break;
		}

		$picture_file = implode(" ", $picture_file);
		$db = new Database();
		$db->call('newProduct', [
			$product_code,
			$amount,
			$hide_amount,
			$price,
			$product_name,
			$product_description,
			$tag_name,
			$picture_file
		]);

	}
	die();
}

$db = new Database();
$result = $db->call('listProduct', []);
if(!$result['success']){
	die('db Error: '.$result['message']);
}
$productList = $result['count']>0?$result['result']:[];

?>
<html>
	<head>
		<style type="text/css">
			.hidden{
				display:none;
			}
		</style>
	</head>
	<body>
		<a href="/api/admin/dashboard/">หน้าหลักแอดมิน</a>
		<h3>รายการสินค้า</h3>
		<button id="newProduct_click">สร้างสินค้าใหม่</button>
		<form id="newProduct_form" class="">
			<table>
				<input type="hidden" name="action" value="newProduct">
				<tr><td style="text-align: right;">รหัสสินค้า (จำกัด 30 ตัวอักษร)</td><td><input type="text" name="product_code"></td></tr>
				<tr><td style="text-align: right;">จำนวนเปิดขาย</td><td><input type="number" name="amount"></td></tr>
				<tr><td style="text-align: right;">จำนวนซ่อนไม่ขาย</td><td><input type="number" name="hide_amount"></td></tr>
				<tr><td style="text-align: right;">ราคา</td><td><input type="number" name="price"></td></tr>
				<tr><td style="text-align: right;">ชื่อสินค้า (จำกัด 100 ตัวอักษร)</td><td><input type="text" name="product_name"></td></tr>
				<tr><td style="text-align: right;">คำอธิบาย</td><td><textarea name="product_description"></textarea></td></tr>
				<tr><td style="text-align: right;">แท็ก เว้นวรรค (เช่น ซิงสาม วันแรก)</td><td><textarea name="tag_name"></textarea></td></tr>
				<tr><td style="text-align: right;">ไฟล์ภาพ (jpg เท่านั้น)</td><td><input type="file" name="picture_file[]" multiple></td></tr>
			</table>
		</form>
		<button id="save_click" class="hidden">บันทึก</button>
		<button id="cancel_click" class="hidden">ยกเลิก</button>
		<table>
				<tr>
					<th>หมวดหมู่</th>
					<th>รหัสสินค้า</th>
					<th>ชื่อสินค้า</th>
					<th>ราคา</th>
					<th title="จำนวนที่กดซื้อได้บนเว็บ">SHOW</th>
					<th title="จำนวนที่ซ่อนไว้ไม่ขาย">HIDE</th>
					<th title="สินค้าที่ติดจองอยู่ในใบสั่งซื้อ"><a href="/api/admin/order/?status=BOOK">BOOK</a></th>
					<th title="สินค้าที่ลูกค้าแจ้งโอนเงินแล้ว รอแอดมินยืนยัน"><a href="/api/admin/order/?status=BILL">BILL</a></th>
					<th title="สินค้าที่จ่ายเงินแล้ว รอแอดพิมพ์ใบแพ็กของ"><a href="/api/admin/order/?status=PAID">PAID</a></th>
					<th title="พิมพ์ใบแพ็กของออกมาแล้ว รอแพ็กของ"><a href="/api/admin/order/?status=PRINT">PRINT</a></th>
					<th title="แพ็กของแล้ว รอส่งของ"><a href="/api/admin/order/?status=PACK">PACK</a></th>
					<th title="ส่งของแล้ว ลงเลขแทร็คให้ลูกค้าแล้ว"><a href="/api/admin/order/?status=SENT">SENT</a></th>
					<th>รวม</th>
				</tr>
			<?php foreach($productList as $product): ?>
				<tr>
					<td><?= $product['category_name'] ?></td>
					<td><?= $product['product_code'] ?></td>
					<td><?= $product['product_name'] ?></td>
					<td><?= $product['price'] ?></td>
					<td><?= $product['show'] ?></td>
					<td><?= $product['hide'] ?></td>
					<td><?= $product['book'] ?></td>
					<td><?= $product['bill'] ?></td>
					<td><?= $product['paid'] ?></td>
					<td><?= $product['print'] ?></td>
					<td><?= $product['pack'] ?></td>
					<td><?= $product['sent'] ?></td>
					<td><?= $product['total'] ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
		<script type="text/javascript">
			$('#newProduct_click').click(function(){
				$(this).addClass('hidden');
				$('#newProduct_form, #save_click, #cancel_click').removeClass('hidden');
				$('#save_click').click(function(){
					$.ajax({
				        type: 'POST',
				        url:"/api/admin/product/",
				        data: new FormData($("#newProduct_form")[0]),
				        processData: false, 
				        contentType: false,
				        //dataType: 
				        success: function(data) {
				           	window.location = window.location;
				         }
				    });
				});
				$('#cancel_click').click(function(){
					window.location = window.location;
				});
			});
			$('#newProduct_click').click();
		</script>
	</body>
</html>