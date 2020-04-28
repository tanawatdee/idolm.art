product_code = $('#i_product_name').attr('data-code');

$('.i_add_cart_click').click(function(){
	$.post('/api/jpost/', {action:'addCart', product_code: product_code}, function(){
		location.reload(true);
	})
});
$('.i_buy_now_click').click(function(){
	$.post('/api/jpost/', {action:'addCart', product_code: product_code}, function(){
		window.location.replace('/cart/');
	})
});
$('.carousel-item').click(function(){
	if($('#product_caro').hasClass('full_screen')){
		if (document.exitFullscreen) {
			document.exitFullscreen();
		} else if (document.webkitExitFullscreen) {
			document.webkitExitFullscreen();
		} else if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
		} else if (document.msExitFullscreen) {
			document.msExitFullscreen();
		}
		$('.caro_h350').removeClass('caro_full');
		$('#product_caro').removeClass('full_screen');
		$('.carousel-item').removeClass('hover_zout');
		$('.carousel-item').addClass('hover_zin');
		$('.a_back').removeClass('hidden');
	}
	else{
		$('.a_back').addClass('hidden');
		$('.carousel-item').removeClass('hover_zin');
		$('.carousel-item').addClass('hover_zout');
		$('#product_caro').addClass('full_screen');
		$('.caro_h350').addClass('caro_full');
		var i = document.body;
		if (i.requestFullscreen) {
			i.requestFullscreen();
		} else if (i.webkitRequestFullscreen) {
			i.webkitRequestFullscreen();
		} else if (i.mozRequestFullScreen) {
			i.mozRequestFullScreen();
		} else if (i.msRequestFullscreen) {
			i.msRequestFullscreen();
		}
	}
});