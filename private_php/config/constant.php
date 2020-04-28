<?php

abstract class ROLE{
	const ADMIN = 0b001;
	const USER  = 0b010;
	const GUEST = 0b100;
	const ALL   = 0b111;
}

abstract class SESS{
	const TIME   = 1800;//30 minutes to seconds
	const C_TIME = 2592000;//30 days to seconds
	const C_TIME_STR = '30 00:00:00';//30 days in MySQL
}

abstract class GEN{
	const BANK_STR = [
		'SCB'=>'ธ.ไทยพาณิชย์ 4072349531 รัชพล มาศผล',
		'KBA'=>'ธ.กสิกรไทย 0371082387 รัชพล มาศผล',
		'BUA'=>'ธ.กรุงเทพ 9390197441 รัชพล มาศผล',
		'KTB'=>'ธ.กรุงไทย 1620304821 รัชพล มาศผล',
		//'TMB'=>'ธ.ทหารไทย 0792305799 รัชพล มาศผล',
		'TRU'=>'ทรูมันนี่ วอลเล็ท 0830245507 รัชพล มาศผล'
	];
	const DELIVERY = ['EMS'=>50, 'REG'=>30, 'KER'=>60];
	const DELIVERY_STR = ['EMS'=>'EMS', 'REG'=>'ลงทะเบียน', 'KER'=>'เคอรี่'];
	const ORDER_EXP_STR = '4:00:00';
	const OMISE_PKEY = 'pkey_test_5b0mgp0v548at8cij0c';
	const OMISE_SKEY = 'skey_test_5b0mgp0vo781bfhfcce';
	const ORDER_STAT_STR = [
		'BOOK' =>'ยังไม่ชำระเงิน',
		'BILL' =>'รอยืนยันชำระเงิน',
		'PAID' =>'ชำระเงินแล้ว',
		'PRINT'=>'จัดเตรียมสินค้า',
		'PACK' =>'จัดเตรียมสินค้า',
		'SENT' =>'จัดส่งสินค้าแล้ว',
		'FAIL' =>'ถูกยกเลิก',
	];
	const ORDER_STAT_COL = [
		'BOOK' =>'warning',
		'BILL' =>'info',
		'PAID' =>'info',
		'PRINT'=>'info',
		'PACK' =>'info',
		'SENT' =>'success',
		'FAIL' =>'danger',
	];
	const ERR_STR = ['SYS'=>'เกิดความผิดพลาดของระบบ โปรดติดต่อ support@idolm.art'];
	const PIC_SIZE = 5242880;//5MB
	const PIC_TYPE = '/^(image.*)$/';
}
?>