$(document).ready(function() {

	var docHeight = $(window).height();
	var footerHeight = $('#footer').height();
	var footerTop = $('#footer').position().top + footerHeight;
	var iScrollPos = 0;
	var isNavHide = false;

	if (footerTop < docHeight) {
		$('#footer').css('margin-top', (docHeight - footerTop) + 'px');
	}

	$('.carousel').bcSwipe({ threshold: 10 });

	$(window).scroll(function(){
		var iCurScrollPos = $(this).scrollTop();
		if(iCurScrollPos - iScrollPos>58&&iCurScrollPos>58&&!isNavHide){
			$('.navbar.trans_top').css('top', '-60px');
			$('#badge_cart.d-sm-none').css('top', '2px');
			isNavHide = true;
			iScrollPos = iCurScrollPos;
		}
		else if(iCurScrollPos - iScrollPos<-58&&isNavHide||iCurScrollPos<58){
			$('.navbar.trans_top').css('top', '0');
			$('#badge_cart.d-sm-none').css('top', '60px');
			isNavHide = false;
			iScrollPos = iCurScrollPos;
		}
		if(Math.abs(iScrollPos-iCurScrollPos)>58) iScrollPos = iCurScrollPos;
	});
});

$('input[type=file]').change(function(){
	$('label[for='+$(this).attr('id')+']').html($(this).val().replace(/.*(\/|\\)/, ''));
});