function renderPrice(){	
	total_price = 0;

	$('.i_product_item').each(function(){
		subtotal_price = $(this).find('.i_amount').val()*$(this).find('.i_amount').attr('data-price');
		$(this).find('.item_subtotal').html(subtotal_price);
		total_price += subtotal_price;
	});

	delivery_fee = delivery_rate[$('#delivery_sel').val()];
	$('#delivery_fee').html(delivery_fee);
	total_price += delivery_fee;

	$('#total_price').html(total_price);
}

$('input, select').change(function(){
	if($(this).hasClass('i_amount')){
		edit_code = $(this).attr('data-code');
		edit_amount = $(this).val();
		$.post('/cart/', {action:'edit', product_code: edit_code, amount: edit_amount}, function(){});
	}
	else if(this.id=='delivery_sel'){
		sel_delivery = $(this).val();
		$.post('/cart/', {action:'delivery', type: sel_delivery}, function(){});
	}
	renderPrice();
});

$('.del_product_click').click(function(){
	del_code = $(this).attr('data-code');
	$.post('/cart/', {action: 'del', product_code: del_code}, function(){
		window.location = window.location;
	});
});

$('#disclaimer_check').change(function(){
	$('#buy_click').prop("disabled", !$(this).is(":checked"));
});

$('#buy_click').click(function(){
	delivery_sel = $('#delivery_sel').val();
	$.post('/cart/', {action: 'order', delivery: delivery_sel}, function(data){
		if(data.success){
			window.location = '/order/' + data.order_code + '/';
		}
		else if(data.err_code == 'ERR_LIMIT'){
			str = "<strong>สินค้าต่อไปนี้มีจำนวนจำกัด</strong><br>";
			for(i=0; i<data.product.length; i++){
				str += data.product[i].product_name + " ไม่เกิน " +
					data.product[i].amount + " ชิ้น <br>";
			}
			$('#err_txt').html(str);
			$('#err_box').removeClass('hidden');
		}
		else{
			$('#err_txt').html('เกิดความผิดพลาดของระบบ โปรดติดต่อ support@idolm.art');
			$('#err_box').removeClass('hidden');
		}
	},'json');
});

renderPrice();