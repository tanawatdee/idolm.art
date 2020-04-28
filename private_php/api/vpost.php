<?php

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$post = json_decode(file_get_contents("php://input"), true);
	$action = $post['action'];
	switch ($action) {

//		

case 'getNavInfo':
	if($session_role&ROLE::USER){
	    $db = new Database();
	    $result = $db->call('countCart', [$_SESSION['info']['id']]);
	    $countCart = $result['result'][0]['count'];
	}
	else{
		$countCart = null;
	}

	if($session_role&ROLE::USER){
		$username = $_SESSION['info']['name'];
	}
	else{
		$username = null;
	}

	die(json_encode([
		'countCart' => $countCart,
		'username' => $username
	]));
break;
		
//

case 'getCaroInfo':
	die(json_encode($landing = json_decode(implode('', file(dirname(__FILE__).'/../config/landing.json')), true)['caro']));
break;
		
//

case 'getProductInfo':
	$db = new Database();
	$result = $db->call('getProduct', [$post['code'], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
	if(!$result['success']||$result['count']<=0){
		die(json_encode(['fail' => true]));
	}
	$product = $result['result'][0];
	$product['picture_file'] = explode(' ', $product['picture_file']);
	$product['tag_name'] = explode(' ', $product['tag_name']);
	foreach ($product['tag_name'] as &$tag) {
		$tag = '<div class="div_ib"><a href="/search/?query='.urlencode('#'.$tag).'">#'.$tag.'</a></div>';
	}
	$product['tag_name'] = implode(' ', $product['tag_name']);

	die(json_encode([
		'picture_file' => $product['picture_file'],
		'product_name' => $product['product_name'],
		'old_price' => $product['old_price'],
		'old_price_format' => number_format($product['old_price']),
		'price' => $product['price'],
		'price_format' => number_format($product['price']),
		'is_amount' => $product['amount'] > 0,
		'tag_name' => $product['tag_name'],
		'product_description' => $product['product_description'],
		'is_login' => ($_SESSION['role']&ROLE::USER) > 0,
		'is_in_cart' => $product['is_in_cart'] == '1',
		'amount' => $product['amount'],
		'book_amount' => $product['book_amount']
	]));
break;

//

case 'addCart':
	$db = new Database();
	$result = $db->call('addCart', [$_SESSION['info']['id'], $post['code']]);
	die();
break;
		
//

case 'delCart':
	$db = new Database();
	$result = $db->call('delCart', [$_SESSION['info']['id'], $post['code']]);
	die();
break;
		
//

case 'editCart':
	if ($post['amount'] <= 0) die();
	$db = new Database();
	$result = $db->call('editCart', [$_SESSION['info']['id'], $post['code'], $post['amount']]);//cal fee

	die(json_encode(['success'=>true, 'delivery'=>calFee()]));
break;
		
//

case 'editDelivery':
	$_SESSION['info']['delivery'] = $post['type'];
	die();
break;
		
//

case 'orderCart':
	$order_code = date('md');
	$pool = array_merge(range(0,9),range('A', 'Z'));
    for($i=0; $i < 6; $i++) {
        $order_code .= $pool[mt_rand(0, 35)];
    }
	$fbid = $_SESSION['info']['id'];
	$delivery_type = $post['type'];
	$delivery_fee = calFee()[$delivery_type];//cal fee
	$order_exp_str = GEN::ORDER_EXP_STR;

	$db = new Database();
	$result = $db->call('newOrder', [
		$order_code,
		$fbid,
	    $delivery_type,
	    $delivery_fee,
	    $order_exp_str
	]);
	if(!$result['success']){
		die(json_encode(['success'=>false, 'err_code'=>'ERR_DB']));
	}
	else if($result['count']>0){
		die(json_encode(['success'=>false, 'err_code'=>'ERR_LIMIT', 'product'=>$result['result']]));
	}
	else{
		die(json_encode(['success'=>true, 'order_code'=>$order_code]));
	}
	die();
break;
		
//

case 'getCartInfo':
	if (!($_SESSION['role']&ROLE::USER)) {
		die(json_encode(['fail' => true]));
	}

	$_SESSION['info']['delivery'] = isset($_SESSION['info']['delivery'])?$_SESSION['info']['delivery']:key(GEN::DELIVERY);
	$delivery_type = $_SESSION['info']['delivery'];

	$db = new Database();
	$result = $db->call('listCart', [$_SESSION['info']['id']]);

	$products = (!$result['success']||$result['count']<=0)?[]:$result['result'];

	$res = ['product' => []];
	foreach ($products as $product) {
		$res['product'][] = [
			'product_code' => $product['product_code'],
			'picture_file' => explode(' ', $product['picture_file'])[0],
			'product_name' => $product['product_name'],
			'amount'	   => $product['amount'],
			'max_amount'   => $product['max_amount'],
			'price'		   => $product['price']
		];
	}
	$res['isCount'] = count($products) > 0;
	$res['delivery_type'] = $delivery_type;
	$res['delivery_str'] = GEN::DELIVERY_STR;//cal fee
	$res['delivery_price'] = calFee();
	$res['order_exp'] = explode(':', GEN::ORDER_EXP_STR)[0];
	die(json_encode($res));
break;
		
//

case 'getProfileInfo':
	if (!($_SESSION['role']&ROLE::USER)) {
		die(json_encode(['fail' => true]));
	}

	$db = new Database();
	$result = $db->call('getFB', [$_SESSION['info']['id']]);
	$address = $result['success']&&$result['count']>0?json_decode($result['result'][0]['address'], true):'';

	$db = new Database();
	$result = $db->call('listOrder', [$_SESSION['info']['id']]);
	$orderList = $result['success']&&$result['count']>0?$result['result']:[];

	$orderListDic = [];
	foreach($orderList as $order){
		$orderListDic[$order['order_code']] = $order;
		$orderListDic[$order['order_code']]['product'] = [];
	}

	$db = new Database();
	$result = $db->call('listOrderProduct', [implode(',', array_keys($orderListDic))]);
	$productList = $result['success']&&$result['count']>0?$result['result']:[];

	foreach ($productList as $product) {
		$orderListDic[$product['order_code']]['product'][] = $product;
	}

	$res = [
		'name' => $_SESSION['info']['name'],
		'email' => $_SESSION['info']['email'],
		'address' => $address
	];
	$order = [];
	foreach ($orderList as $suborder) {
		$order[] = [
			'order_code' => $suborder['order_code'],
			'tracking_no' => $suborder['tracking_no'],
			'status_class' => 'badge-'.GEN::ORDER_STAT_COL[$suborder['status']],
			'status_str' => GEN::ORDER_STAT_STR[$suborder['status']],
			'order_time' => date("d/m/y", strtotime($suborder['order_time'])),
			'total_price' => number_format(json_decode($suborder['payment_detail'], true)['total_price']),
			'link_str' => $suborder['status']=='BOOK'?'ไปยังหน้าชำระเงิน':'รายละเอียดเพิ่มเติม',
			'product' => $orderListDic[$suborder['order_code']]['product']
		];
	}
	$res['order'] = $order;
	die(json_encode($res));
break;
		
//

case 'logout':
	if(isset($_COOKIE['fblogin'])) {
		$db = new Database();
		$db->call('delCookie', [$_COOKIE['fblogin']]);
	}

	$_SESSION['role'] = ROLE::GUEST;
	$_SESSION['info'] = null;
	setcookie('fblogin', '', time() - 3600, '/');
	die(json_encode(true));
break;
		
//

case 'editProfile':
	$post_name    = $post['name'];
	$post_email   = $post['email'];
	$post_address = $post['address'];

	$db = new Database();
	$result = $db->call('editFB', [
		$_SESSION['info']['id'],
		$post_name,
		$post_email,
		$post_address
	]);
	if(!$result['success']){
		die();
	}

	$_SESSION['info']['name'] = $post_name;
	$_SESSION['info']['email'] = $post_email;
	die();
break;
		
//

case 'getOrderInfo':
	$db = new Database();
	$result = $db->call('getOrder', [$post['code'], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
	if(!$result['success']||$result['count']<=0){
		die(json_encode(['fail' => true]));
	}
	$order_info = $result['result'][0];
	$order_info['payment_detail'] = json_decode($order_info['payment_detail'], true);
	$order_info['badge_type'] = GEN::ORDER_STAT_COL[$order_info['status']];
	$order_info['address_show'] = $order_info['status']=='BOOK'?$order_info['address']:$order_info['payment_detail']['address'];
	$address = json_decode($order_info['address_show'], true);

	$result = $db->call('getOrder_product', [$post['code'], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
	if(!$result['success']||$result['count']<=0){
		die(json_encode(['fail' => true]));
	}
	$products = $result['result'];

	die(json_encode([
		'order_code' => $order_info['order_code'],
		'status_class' => 'badge-'.$order_info['badge_type'],
		'status_str' => GEN::ORDER_STAT_STR[$order_info['status']],
		'is_bill' => $order_info['status']=='BILL',
		'is_book' => $order_info['status']=='BOOK',
		'is_sent' => $order_info['status']=='SENT',
		'is_fail' => $order_info['status']=='FAIL',
		'is_discount' => $order_info['discount']!=0,
		'discount' => $order_info['discount'],
		'tracking_no' => $order_info['status']=='SENT' ? $order_info['tracking_no'] : null,
		'delivery_str' => GEN::DELIVERY_STR[$order_info['delivery_type']],
		'delivery_fee' => $order_info['delivery_fee'],
		'total_price' => $order_info['payment_detail']['total_price'],
		'name' => $_SESSION['info']['name'],
		'email' => $_SESSION['info']['email'],
		'address' => $address,
		'address_str' => $order_info['address'],
		'bank' => GEN::BANK_STR,
		'expire_str' => date("d/m/y เวลา H:i น.", strtotime($order_info['expire_time'])),
		'place_price' => $order_info['payment_detail']['total_price'].'.00',
		'product' => $products,
		'payment_detail' => $order_info['payment_detail']
	]));
break;

//

case 'getSearchInfo':
	$_SESSION['tag_sel'] = $post['query'];

	$landing = json_decode(implode('', file(dirname(__FILE__).'/../config/landing.json')), true);

	$db = new Database();
	$result = $db->call('recommendProduct', [implode(',', $landing['recommended'])]);
	$result = $result['success']&&$result['count']>0?$result['result']:[];
	$recommended= [];
	foreach($result as $rec){
		$recommended[$rec['product_code']] = $rec;
		$recommended[$rec['product_code']]['price_str'] = number_format($recommended[$rec['product_code']]['price']);
		$recommended[$rec['product_code']]['old_price_str'] = number_format($recommended[$rec['product_code']]['old_price']);
	}

	if(isset($post['query'])&&trim($post['query'])==''){
		unset($post['query']);
	}

	$tag = [];
	$text = [];
	if(isset($post['query'])){
		$query = explode(' ', $post['query']);
		foreach($query as $subq){
			$subq = trim($subq);
			if($subq[0] == '#'){
				$subq = trim(end(explode('#', $subq)));
				if($subq != ''){
					$tag[] = $subq;
				}
			}
			else if($subq != ''){
				$text[] = $subq;
			}
		}
	}

	$db = new Database();
	$tag = array_merge($tag, $text);
	if(count($tag)){
		$result = $db->call('tagSearch', [implode(',', $tag)]);
		$tag = $result['success']&&$result['count']>0?$result['result']:[];
	}
	else{
		$tag = [];
	}
	// if(count($text)){
	// 	$result = $db->call('textSearch', [implode(' ', $text)]);
	// 	$text = $result['success']&&$result['count']>0?$result['result']:[];
	// }
	// else{
	// 	$text = [];
	// }
	$text = [];

	$productList = [];
	$score = count($tag)?$tag[0]['score']:0;
	foreach ($tag as $tag_elm) {
		if($tag_elm['score']!=$score) break;
		$productList[] = $tag_elm['product_code'];
	}
	foreach ($text as $text_elm) {
		$productList[] = $text_elm['product_code'];
	}
	foreach ($tag as $tag_elm) {
		if($tag_elm['score']==$score) continue;
		$productList[] = $tag_elm['product_code'];
	}

	$productList = array_unique($productList);

	$result = $db->call('recommendProduct', [implode(',', $productList)]);
	$result = $result['success']&&$result['count']>0?$result['result']:[];
	$search_result= [];
	foreach($result as $rec){
		$search_result[$rec['product_code']] = $rec;
		$search_result[$rec['product_code']]['price_str'] = number_format($search_result[$rec['product_code']]['price']);
		$search_result[$rec['product_code']]['old_price_str'] = number_format($search_result[$rec['product_code']]['old_price']);
	}

	die(json_encode([
		'tag' => $landing['tag'],
		'search' => [
			'result' => $search_result,
			'product' => $productList
		],
		'recom' => [
			'result' => $recommended,
			'product' => $landing['recommended']
		]
	]));
break;

//

case 'getLandingInfo':
	$landing = json_decode(implode('', file(dirname(__FILE__).'/../config/landing.json')), true);

	$db = new Database();
	$result = $db->call('recommendProduct', [implode(',', $landing['recommended'])]);
	$result = $result['success']&&$result['count']>0?$result['result']:[];
	$recommended = [];
	foreach($result as $rec){
		$recommended[$rec['product_code']] = $rec;
		$recommended[$rec['product_code']]['price_str'] = number_format($recommended[$rec['product_code']]['price']);
		$recommended[$rec['product_code']]['old_price_str'] = number_format($recommended[$rec['product_code']]['old_price']);
	}

	$result = $db->call('allProduct', []);
	$allProduct = $result['success']&&$result['count']>0?$result['result']:[];

	foreach($allProduct as &$rec){
		$rec['price_str'] = number_format($rec['price']);
		$rec['old_price_str'] = number_format($rec['old_price']);
	}

	die(json_encode([
		'cat' => $landing['category'],
		'all' => $allProduct,
		'recom' => [
			'result' => $recommended,
			'product' => $landing['recommended']
		],
		'member' => $landing['member']
	]));

break;
		
//-----------------------------------------------------------------------------------------------------------------------------

default:
	switch ($_POST['action']) {

//		

case 'confirmTransfer':
	if($_FILES['transfer_pic']['size']>GEN::PIC_SIZE){
		die(json_encode([
			'success'=>false,
			'err_code'=>'ERR_SIZE'
		]));
	}
	$tmp_name = $_FILES['transfer_pic']['tmp_name'];
	if(!preg_match(GEN::PIC_TYPE, mime_content_type($tmp_name))){
		die(json_encode([
			'success'=>false,
			'err_code'=>'ERR_TYPE'
		]));
	}
	$address = json_decode($_POST['order_address'], true);
	if(
		trim($address['name']) == '' ||
		trim($address['tel']) == '' ||
		trim($address['place']) == '' ||
		trim($address['subdistrict']) == '' ||
		trim($address['district']) == '' ||
		trim($address['province']) == '' ||
		trim($address['post']) == ''
	){
		die(json_encode([
			'success'=>false,
			'err_code'=>'ERR_ADDR'
		]));
	}

	$picture_file = $_POST['order_code'].'.'.pathinfo($_FILES['transfer_pic']['name'], PATHINFO_EXTENSION);
	$realpath_file = dirname(__FILE__).'/../upload/paid_confirm/'.$picture_file;
	move_uploaded_file($tmp_name, $realpath_file);

	$db = new Database();
	$result = $db->call('chargeOrder', [$_POST['order_code'], ($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null]);
	if(!$result['success']||$result['count']<=0){
		die(json_encode([
			'success'=>false,
			'err_code'=>'ERR_SYS'
		]));
	}
	$total_price = json_decode($result['result'][0]['payment_detail'], true)['total_price'];
	$payment_detail = json_encode([
		'address'	     =>$_POST['order_address'],
		'target_bank'    =>$_POST['target_bank'],
		'transfer_date'  =>$_POST['transfer_date'],
		'transfer_time'  =>$_POST['transfer_time'],
		'transfer_amount'=>$_POST['transfer_amount'],
		'total_price'    =>$total_price,
	]);
	$result = $db->call('billOrder', [
		$_POST['order_code'],
		($_SESSION['role']&ROLE::USER)?$_SESSION['info']['id']:null,
		$payment_detail,
		$picture_file
	]);
	if(!$result['success']){
		die(json_encode([
			'success'=>false,
			'err_code'=>'ERR_SYS'
		]));
	}
	if($result['result'][0][0]<=0){
		die(json_encode([
			'success'=>false,
			'err_code'=>'ERR_SYS'
		]));
	}

	include_once dirname(__FILE__).'/messenger.php';

	(new Messenger())->sendApprove(
		$_POST['order_code'],
		$total_price,
		$_SESSION['info']['name'],
		(new Messenger())->uploadImage($realpath_file, mime_content_type($realpath_file))
	);

	die(json_encode([
		'success'=>true
	]));
break;

//

	}
break;
	}
}
else{
	header('Location: /');
}

function calFee(){
	$db = new Database();

	$result = $db->call('listCart', [$_SESSION['info']['id']]);
	$products = (!$result['success']||$result['count']<=0)?[]:$result['result'];

	$group = [];
	foreach($products as $product){
		$group[$product['category']?:'null_1000'] = ($group[$product['category']?:'null_1000']?:0) + $product['amount'];
	}
	$factor = 0;
	$add_delivery = [];//additional fee for some product type
	foreach($group as $k_group => $v_group){
		$factor += ceil($v_group/explode('_', $k_group)[1]);
		if($k_group == 'CY_1'){
			$add_delivery['KER'] = ($add_delivery['KER']?:0) + 40*ceil($v_group/explode('_', $k_group)[1]);
		}
	}
	$delivery = [];
	foreach (GEN::DELIVERY as $k_delivery => $v_delivery) {
		$delivery[$k_delivery] = $factor*$v_delivery + ($add_delivery[$k_delivery]?:0);
	}

	return $delivery;
}

function calFee2(){
	$db = new Database();

	$result = $db->call('listCart', [$_SESSION['info']['id']]);
	$products = (!$result['success']||$result['count']<=0)?[]:$result['result'];

	$group = [];
	foreach($products as $product){
		$group[$product['category']?:'null_1000'] = ($group[$product['category']?:'null_1000']?:0) + $product['amount'];
	}
	$factor = 0;
	foreach($group as $k_group => $v_group){
		$factor += ceil($v_group/explode('_', $k_group)[1]);
	}
	$delivery = [];
	foreach (GEN::DELIVERY as $k_delivery => $v_delivery) {
		$delivery[$k_delivery] = $factor*$v_delivery;
	}

	return $delivery;
}

?>