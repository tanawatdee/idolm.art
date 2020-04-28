<?php

abstract class ROUTE{
	const MAP =  [
		['admin\/payment_file\/.+', ROLE::ADMIN	   , 'page/admin/payment_file.php', false],
		['admin\/print_order'     , ROLE::ADMIN	   , 'page/admin/print_order.php' , false],
		['admin\/product'  , ROLE::ADMIN		   , 'page/admin/product.php'  , false],
		['admin\/order'    , ROLE::ADMIN		   , 'page/admin/order.php'    , false],
		['admin\/account'  , ROLE::ADMIN		   , 'page/admin/account.php'  , false],
		['admin\/chat'     , ROLE::ADMIN		   , 'page/admin/chat.php'     , false],
		['admin\/upElect'  , ROLE::ADMIN		   , 'page/admin/upElect.php'  , false],
		['admin\/paidElect', ROLE::ADMIN		   , 'page/admin/paidElect.php', false],
		['admin\/edit'     , ROLE::ADMIN		   , 'page/admin/edit.php'     , false],
		['admin\/dashboard', ROLE::ADMIN		   , 'page/admin/dashboard.php', false],
		['admin\/login'    , ROLE::ALL 			   , 'page/admin/login.php'    , false],
		// ['profile'         , ROLE::USER            , 'page/profile.php'        , true],
		// ['cart'            , ROLE::USER            , 'page/cart.php'           , true],
		// ['order\/\w+'      , ROLE::USER            , 'page/order.php'          , true],
		// ['paid_confirm'    , ROLE::USER            , 'page/paid_confirm.php'   , false],
		// ['omise'           , ROLE::USER            , 'page/omise.php'          , false],
		// ['product\/\w+'    , ROLE::ALL 			   , 'page/product.php'        , false],
		// ['search'          , ROLE::ALL 			   , 'page/search.php'         , false],
		// ['login'           , ROLE::ALL 			   , 'page/login.php'          , false],
		// ['policy'          , ROLE::ALL             , 'page/policy.php'         , false],
		// ['terms'           , ROLE::ALL             , 'page/terms.php'          , false],
		// ['testcss'         , ROLE::ALL             , 'page/testcss.php'        , false],
		//['mailer'         , ROLE::ALL             , 'api/mailer.php'         , false],

		['payment_file\/.+', ROLE::USER	           , 'page/admin/payment_file.php', false],
		['bothook'         , ROLE::ALL             , 'api/bothook.php'         , false],
		['fblogin'         , ROLE::ALL             , 'api/fblogin.php'         , false],
		['jpost'           , ROLE::ALL             , 'api/jpost.php'           , false],
		['vpost'           , ROLE::ALL             , 'api/vpost.php'           , false],
		['base64'          , ROLE::ALL             , 'api/base64.php'          , false]
		// ['{0}'	           , ROLE::ALL             , 'page/index.php'          , false]
	];
}

?>