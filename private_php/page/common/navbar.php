<?php

if($session_role&ROLE::USER){
    $db = new Database();
    $result = $db->call('countCart', [$_SESSION['info']['id']]);
    $countCart = $result['result'][0]['count'];
}

?>
<nav class="navbar navbar-expand fixed-top color_nav trans_top">
    <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
        <ul class="navbar-nav mr-auto">
            <form class="form-inline" action="/search/">
                <div class="input-group margin_left15">
                    <button class="btn btn-danger rounded-0 my-sm-0" type="submit">&#8981;</button>
                    <input name="query" type="search" class="form-control rounded-0 mr-sm-2" placeholder="ค้นหา" aria-label="search" aria-describedby="basic-addon1">
                </div>
            </form>
        </ul>
    </div>
    <div class="mx-auto order-0">
        <a class="navbar-brand mx-auto hover_pointer" href="/">
            <div class="nav-item">
                <img src="/assets/img/nav/logo.svg" height="58">
            </div>
        </a>
    </div>
    <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
        <ul class="navbar-nav ml-auto">
            <?php if(($session_role&ROLE::USER)&&$countCart>0): ?>
            <li class="nav-item margin_right15 d-none d-sm-block">
                <a href="/cart/">
                    <button id="badge_cart" type="button" class="btn btn-danger"><img src="/assets/img/nav/cart_icon.png" height="20"> <span class="badge badge-light"><?=$countCart?></span>
                </button></a>
            </li>
            <?php endif; ?>
            <li class="nav-item mr-2">
            	<?php if($session_role&ROLE::USER): ?>
					<a class="btn btn-danger rounded-0 d-none d-sm-block" href="/profile/"><?= $_SESSION['info']['name'] ?></a>
					<a class="btn btn-danger rounded-0 d-block d-sm-none" href="/profile/">ข้อมูลการซื้อ</a>
				<?php else: ?>
	                <button type="button" class="btn btn-primary loginFB_click">
	                	<img src="/assets/img/nav/fb_white.png" height="28"> ล็อกอิน
	                </button>
                <?php endif; ?>
            </li>
        </ul>
    </div>
    <?php if(($session_role&ROLE::USER)&&$countCart>0): ?>
        <a href="/cart/"><button id="badge_cart" type="button" class="btn btn-light d-block d-sm-none p-0 trans_top">
          <img src="/assets/img/nav/cart_icon.png" height="20"><span class="badge badge-light"><?=$countCart?></span>
        </button></a>
    <?php endif; ?>
</nav>