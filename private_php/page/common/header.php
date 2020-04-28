<meta charset="utf-8">
<meta name="viewport" content="width=370, initial-scale=1, shrink-to-fit=no">
<title><?= $head_title ?></title>
<link rel="shortcut icon" href="/assets/img/nav/logo_pink.png"/>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
<link rel="stylesheet" href="/assets/js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
<style type="text/css">
	html{
		margin: 0;
		padding: 0;
		height: 100%;
		font-family: 'Prompt', sans-serif;
	}
	body{
		margin-top: 58px;
		height: -moz-calc(100% - 58px); /* Firefox */
		height: -webkit-calc(100% - 58px); /* Chrome, Safari */
		height: calc(100% - 58px); /* IE9+ and future browsers */
		font-family: 'Prompt', sans-serif;
	}

	.caro_h350{
		height: 100%;
	}
	.caro_hvw{
		height: 75vw;
	}
	.a_back{
	    position: absolute;
	    left: 20px;
	    top: 10px;
	    z-index: 10;
	}
		
	@media (min-width: 576px) {
		.caro_hvw{
			height: 25vw;
		}
	}

	@media (min-width: 768px) {
		.caro_h100{ 
			height: 100%;
		}
	}

	@media (max-width: 767.98px) {
		.caro_h350{
			height: 350px;
		}
		.caro_full{
			transform: translateY(50vh) translateY(-200px);
		}
	}

	.navbar.fixed-top{
		box-shadow: 0 1px 8px rgba(0,0,0,.3);
	}

	.color_nav{
		background: linear-gradient(to bottom right, #FF9999, #FF0066);
	}

	#badge_cart.d-sm-none{
		position: fixed;
		right: 0;
		top: 60px;
	}

	.text_wrap{
		word-wrap: break-word;
	}

	.product_box{
		width: 100%;
		height: 120px;
		text-align: left;
		display: inline-block;
	}
	.category_box{
		width: 100%;
		height: 120px;
		text-align: center;
		display: inline-block;
	}
	.price_sale{
		margin-top: -10px;
    	font-size: 2em;
	}

	.del_product{
		width: 25px;
		height: 25px;
		position: absolute;
		top: 0;
		right: 0;
		text-align: center;
		color: blue;
		z-index: 10;
		cursor: pointer;
	}
	.del_product:hover{
		color: red;
	}

	.img_bg{
		background-repeat: no-repeat;
		background-size: contain;
		background-position: center;
	}
	.img_bg_cov{
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center;
	}
	.img_credit_logo{
		height: 25px;
	}

	.min_h100{
		min-height: 100%;
	}
	.w140px{
		width: 140px;	
	}
	.w110px{
		width: 110px;
	}
	.mw100px{
		max-width: 100px;
	}
	.wh120px{
		width: 120px;
		height: 120px;
	}
	.whi75px{
		width: 75px;
		height: 75px;
		display: inline-block;
	}
	.fs13em{
		font-size: 1.3em;
	}
	.ovy_auto{
		overflow-y: auto;
	}
	.div_ib{
		display: inline-block;
	}
	.full_screen{
	    position: fixed;
	    top: 0;
	    right: 0;
	    left: 0;
	    bottom: 0;
	    z-index: 1050;
	    height: 100%;
	    width: 100%;
	}
	.trans_top{
		transition: top 0.2s ease-in-out;
	}

	.navbar, .navbar-brand{
		padding: 0;
	}
	.loginFB_click{
		background-color: #4267b2;
		border-color: #4267b2;
	}

	.margin_left15{
		margin-left: 15px;
	}
	.margin_right15{
		margin-right: 15px;
	}

	.hover_default{
		cursor: default;
	}
	.hover_pointer{
		cursor: pointer;
	}
	.hover_zin{
		cursor: zoom-in;
	}
	.hover_zout{
		cursor: zoom-out;
	}

	.hidden{
		display: none;
	}
</style>