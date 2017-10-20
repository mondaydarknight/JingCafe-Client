(function($) {

	$(document).on('cartLoad', '#cart', function(event, products) {
		event.preventDefault();
		var cartContent = [];
		var $cart = $(this);

		$(this).find('span.amount').text(products.length);
		$.each(products, function(index, product) {
			if (index > 3) {
				cartContent.push('<li role="separator" class="divider"></li>');
				return false;
			}

			cartContent.push(
				'<li>'+
				'<div class="pull-right">'+
				'<button class="close" aria-label="Close" data-dismiss="cart"><span aria-hidden="true">&times</span></button>'+
				'</div>'+
				'<a href="/JiCoffee/view/product/detail/?serial='+ product.serial +'">'+
				'<div class="pull-left">'+
				'<img src="/JiCoffee/images/product/normal/'+ product.image +'" class="img-circle">'+
				'</div>'+
				'<h4>'+ product.name + 'x' + product.amount +'</h4>'+
				'<p>NT$ '+ product.totalPrice +'</p>'+
				'</a>'+
				'</li>'
			);
		});

		$(this).find('ul.menu').html(cartContent).end()
			.on('click.closeItem', 'button.close', function(event) {
			
			event.preventDefault();
			var index = $cart.find('button.close').index(this);

			products.splice(index, 1);
			$(this).closest('li').fadeOut();
			$cart.find('span.amount').text(products.length);

			sessionStorage.setItem('product', JSON.stringify(products));
		});

	})
	// .on('list', '#productList', function(event, products) {
	// 	event.preventDefault();
	// 	var list = [];
	// 	var total = 0;
			
	// 	$.each(products, function(index, product) {
	// 		list.push(
	// 			'<tr>'+
	// 			'<td><img src="/JiCoffee/images/product/normal/'+ product.image +'" class="img-thumbnail img-small"></td>'+
	// 			'<td class="productName"><a href="/JiCoffee/view/product/detail/?serial='+ product.serial +'">'+ product.name +'</a></td>'+
	// 			'<td>NT$ <span class="price">'+ product.price +'</span></td>'+
	// 			'<td class="input-group"><span class="amount" data-productid="'+product.productid+'">'+ product.amount +'</span></td>'+
	// 			'<td>NT$ <span class="totalprice">'+ product.totalprice +'</span></td>'+
	// 			'</tr>'
	// 		);
			
	// 		total += parseInt(product.totalprice);
	// 	});

	// 	$(this).find('tbody').html(list).end()
	// 		.next().find('span.originPrice, span.totalPrice').text(total);
	// });

}(jQuery))