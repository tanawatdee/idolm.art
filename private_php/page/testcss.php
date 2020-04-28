<?php

$landing = json_decode(implode('', file(dirname(__FILE__).'/../config/landing.json')), true);

$db = new Database();
$result = $db->call('recommendProduct', [implode(',', $landing['recommended'])]);
$result = $result['success']&&$result['count']>0?$result['result']:[];
$recommended= [];
foreach($result as $rec){
	$recommended[$rec['product_code']] = $rec;
}

$result = $db->call('allProduct', []);
$allProduct = $result['success']&&$result['count']>0?$result['result']:[];

?>
<!DOCTYPE html>
<html>
	<head>
		<?php $head_title = 'Idolm.art'; ?>
		<?php include(dirname(__FILE__).'/common/header.php'); ?>
	</head>
	<body>
		<?php include(dirname(__FILE__).'/common/navbar.php'); ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 pl-0 pr-0">
					<div id="carouselExampleIndicators" class="carousel slide w-100 caro_hvw" data-interval="10000" data-ride="carousel" data-pause="hover">
					  	<ol class="carousel-indicators">
					  		<?php foreach ($landing['caro'] as $i => $caro): ?>
						    <li data-target="#carouselExampleIndicators" data-slide-to="<?=$i?>" class="hover_pointer hidden <?=$i==0?'active':''?>"></li>
							<?php endforeach; ?>
					  	</ol>
					  	<div class="carousel-inner h-100 w-100">
					  		<?php foreach ($landing['caro'] as $i => $caro): ?>
						  	<div class="carousel-item h-100 w-100 <?=$i==0?'active':''?>">
						  		<a href="<?=$caro['url']?>">
						  			<div class=" h-100 w-100 d-none d-sm-block">
						    			<div class="d-block h-100 w-100 img_bg_cov" style="background-image: url('/assets/img/caro/<?=$caro['pic_l']?>');"></div>
						    		</div>
						    		<div class="h-100 w-100 d-block d-sm-none">
						    			<div class="d-block h-100 w-100 img_bg_cov" style="background-image: url('/assets/img/caro/<?=$caro['pic_s']?>');"></div>
						    		</div>
						    	</a>
						    </div>
						    <?php endforeach; ?>
					  	</div>
					  	<a class="carousel-control-prev hidden" href="#carouselExampleIndicators" role="button" data-slide="prev">
					    	<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					    	<span class="sr-only">Previous</span>
					  	</a>
					  	<a class="carousel-control-next hidden" href="#carouselExampleIndicators" role="button" data-slide="next">
					    	<span class="carousel-control-next-icon" aria-hidden="true"></span>
					    	<span class="sr-only">Next</span>
					  	</a>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col"><h5 class="text-info">Idolm.art (ไอดอลมาร์ต) คือร้านค้าออนไลน์ที่จำหน่ายสินค้าไอดอลวงต่างๆ เช่น BNK48, Sweat16! เพื่อความสะดวกท่านสามารถติดตามข่าวสารได้ที่<a href="//facebook.com/page.idolm.art/" target="_blank">แฟนเพจของเรา</a> และเพื่อความมั่นใจในการจับจ่ายใช้สอยของท่าน สามารถตรวจสอบได้ที่<a href="/about/" target="_blank">ข้อมูลร้านของเรา</a></h5></div>
			</div>
			<div class="row">
				<div class="col-12 pl-0 pr-0">
					<h5 class="card-header">ของมันต้องมี</h5>
					<div class="card-body pl-0 pr-0 pt-0">
						<div class="row ml-0 mr-0 pl-0 pr-0 pt-0">
							<?php foreach($landing['recommended'] as $rec): ?>
							<div class="col-12 col-md-6 col-xl-4 mt-3 text-center">
								<a href="/product/<?=$recommended[$rec]['product_code']?>/" class="text-dark">
									<div class="product_box">
										<div class="row">
											<div class="col-auto">
												<div class="img_bg_cov wh120px" style="background-image: url('/assets/img/product/<?=$recommended[$rec]['picture_file']?>');"></div>
											</div>
											<div class="col">
												<div class="row h-75">
													<h5 class="mt-2">
														<?=$recommended[$rec]['product_name']?>
														<?php if($recommended[$rec]['old_price']!=null): ?>
														<span class="badge badge-danger">Sale!!!</span>
														<?php endif; ?>
													</h5>
												</div>
												<div class="row h-25">
													<span class="<?=$recommended[$rec]['old_price']==null?'':'text-danger price_sale'?>">฿<?=number_format($recommended[$rec]['price'])?></span>&emsp;
													<?php if($recommended[$rec]['old_price']!=null): ?>
													<span class="text-secondary" style="text-decoration: line-through;">฿<?=number_format($recommended[$rec]['old_price'])?></span>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</a>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<h5 class="card-header">หมวดหมู่</h5>
					<div class="card-body pl-0 pr-0 pt-0">
						<div class="row ml-0 mr-0 pl-0 pr-0 pt-0">
							<?php foreach($landing['category'] as $cat): ?>
							<div class="col-6 col-sm-3 col-md-2 mt-3 text-center">
								<a href="/search?query=<?=urlencode('#'.$cat['name'])?>">
									<div class="category_box">
										<div class="img_bg_cov whi75px" style="background-image: url('/assets/img/category/<?=$cat['pic']?>');"></div><br>
										#<?=$cat['name']?>
									</div>
								</a>
							</div>
							<?php endforeach; ?>
						</div>
					</div>
					<h5 class="card-header">สินค้าทั้งหมด</h5>
						<div class="card-body pl-0 pr-0 pt-0">
							<div class="row ml-0 mr-0 pl-0 pr-0 pt-0">
								<?php foreach($allProduct as $rec): ?>
								<div class="col-12 col-md-6 col-xl-4 mt-3 text-center">
									<a href="/product/<?=$rec['product_code']?>/" class="text-dark">
										<div class="product_box">
											<div class="row">
												<div class="col-auto">
													<div class="img_bg_cov wh120px" style="background-image: url('/assets/img/product/<?=$rec['picture_file']?>');"></div>
												</div>
												<div class="col">
													<div class="row h-75">
														<h5 class="mt-2">
															<?=$rec['product_name']?>
															<?php if($rec['old_price']!=null): ?>
															<span class="badge badge-danger">Sale!!!</span>
															<?php endif; ?>
														</h5>
													</div>
													<div class="row h-25">
														<span class="<?=$rec['old_price']==null?'':'text-danger price_sale'?>">฿<?=number_format($rec['price'])?></span>&emsp;
														<?php if($rec['old_price']!=null): ?>
														<span class="text-secondary" style="text-decoration: line-through;">฿<?=number_format($rec['old_price'])?></span>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									</a>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include(dirname(__FILE__).'/common/script.php'); ?>
	</body>
</html>