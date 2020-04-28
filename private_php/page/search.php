<?php

$landing = json_decode(implode('', file(dirname(__FILE__).'/../config/landing.json')), true);

$db = new Database();
$result = $db->call('recommendProduct', [implode(',', $landing['recommended'])]);
$result = $result['success']&&$result['count']>0?$result['result']:[];
$recommended= [];
foreach($result as $rec){
	$recommended[$rec['product_code']] = $rec;
}

if(isset($_GET['query'])&&trim($_GET['query'])==''){
	unset($_GET['query']);
}

$tag = [];
$text = [];
if(isset($_GET['query'])){
	$query = explode(' ', $_GET['query']);
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
}

?>
<!DOCTYPE html>
<html>
	<head>
		<?php $head_title = isset($_GET['query'])?$_GET['query'].' - ค้นหา':'ค้นหา'; ?>
		<?php include(dirname(__FILE__).'/common/header.php'); ?>
	</head>
	<body>
		<?php include(dirname(__FILE__).'/common/navbar.php'); ?>
		<div class="container-fluid">
			<div class="row">
				<div class="col mt-3"><h1>ค้นหาสินค้า</h1></div>
			</div>
			<div class="row">
				<div class="col-12 col-lg-6">	
		            <form class="form-inline" action="/search/">
		                <div class="input-group w-100">
		                    <input name="query" type="search" class="form-control rounded-0" placeholder="ค้นหา" aria-label="search" aria-describedby="basic-addon1" <?=isset($_GET['query'])?'value="'.htmlspecialchars($_GET['query']).'"':''?>>
		                    <button class="btn btn-primary rounded-0 my-sm-0" type="submit">&#8981;</button>
		                </div>
		            </form>
				</div>
			</div>
			<div class="row">
				<div class="col-12 mt-2 pl-4">
					<span class="card-subtitle text-muted">แนะนำ: </span>
					<?php foreach($landing['tag'] as $suggest_tag): ?>
					<div class="div_ib"><a href="/search/?query=%23<?=$suggest_tag?>">#<?=$suggest_tag?></a></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="row">
				<div class="col-12 pl-0 pr-0 mt-3">
					<? if(isset($_GET['query'])): ?>
					<h5 class="card-header">ผลการค้นหา <?=htmlspecialchars($_GET['query'])?></h5>
					<div class="card-body pl-0 pr-0 pt-0">
						<?php if(count($search_result)): ?>
						<div class="row ml-0 mr-0 pl-0 pr-0 pt-0">
							<?php foreach($productList as $rec): ?>
							<div class="col-12 col-md-6 col-xl-4 mt-3 text-center">
								<a href="/product/<?=$search_result[$rec]['product_code']?>/?origin=<?=urlencode($request_uri)?>" class="text-dark">
									<div class="product_box">
										<div class="row">
											<div class="col-auto">
												<div class="img_bg_cov wh120px" style="background-image: url('/assets/img/product/s_<?=$search_result[$rec]['picture_file']?>');"></div>
											</div>
											<div class="col">
												<div class="row h-75">
													<h5 class="mt-2">
														<?=$search_result[$rec]['product_name']?>
														<?php if($search_result[$rec]['old_price']!=null): ?>
														<span class="badge badge-danger">Sale!!!</span>
														<?php endif; ?>
													</h5>
												</div>
												<div class="row h-25">
													<span class="<?=$search_result[$rec]['old_price']==null?'':'text-danger price_sale'?>">฿<?=number_format($search_result[$rec]['price'])?></span>&emsp;
													<?php if($search_result[$rec]['old_price']!=null): ?>
													<span class="text-secondary" style="text-decoration: line-through;">฿<?=number_format($search_result[$rec]['old_price'])?></span>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</a>
							</div>
							<?php endforeach; ?>
						</div>
						<?php else: ?>
						<div class="row ml-3 mt-3 mr-3">
							<div class="col">
								<div class="alert alert-light" role="alert">
								  	ไม่พบผลการค้นหา
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<h5 class="card-header">ของมันต้องมี</h5>
					<div class="card-body pl-0 pr-0 pt-0">
						<div class="row ml-0 mr-0 pl-0 pr-0 pt-0">
							<?php foreach($landing['recommended'] as $rec): ?>
							<div class="col-12 col-md-6 col-xl-4 mt-3 text-center">
								<a href="/product/<?=$recommended[$rec]['product_code']?>/?origin=<?=urlencode($request_uri)?>" class="text-dark">
									<div class="product_box">
										<div class="row">
											<div class="col-auto">
												<div class="img_bg_cov wh120px" style="background-image: url('/assets/img/product/s_<?=$recommended[$rec]['picture_file']?>');"></div>
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
				</div>
			</div>
		</div>
		<?php include(dirname(__FILE__).'/common/script.php'); ?>
	</body>
</html>