(function($) {
	'use strict';

	var Cart;


	Cart = (function() {

		function Cart(elem, options) {
			this.elem = elem;
			this.options = options;
		}

		Cart.prototype = {
			
			defaults: {
				cartItem: [],
				product: []
			},

			init: function() {
				this.defaults = $.extend({}, this.defaults, this.options);
				this.generateCart();
				this.loadItem();
			},

			generateCart: function() {
				this.elem
					.addClass('dropdown messages-menu')
					.html(
						'<a href="#" class="dropdown-toggle" data-toggle="dropdown">'+
						'<i class="fa fa-shopping-cart fa-lg"></i>'+
						'<span class="label label-success amount"></span>'+
						'</a>'+
						'<ul class="dropdown-menu">'+
						'<li class="header">您選購<span class="amount"></span>項目</li>'+
						'<li>'+
						'<ul class="menu"></ul>'+
						'<li class="footer"><a href="/JiCoffee/view/pay/">結帳</a></li>'+
						'</li>'+
						'</ul>'
					);
			},

			loadItem: function() {

				if (this.defaults.product.length > 0) {
					$.each(this.defaults.product, $.proxy(this.loadItemProcess, this));
					
					this.elem
						.find('span.amount').text(this.defaults.product.length).end()
						.find('ul.menu').html(this.defaults.cartItem)
						.on('click.removeItem', 'button.close', {cart: this}, this.removeItem);
				}
			},

			loadItemProcess: function(index, product) {
				// if (index > 3) {
				// 	this.defaults.cartItem.push('<li role="separator" class="divider"></li>');
				// 	return false;
				// }

				this.defaults.cartItem.push(
					'<li>'+
					'<div class="pull-right">'+
					'<button class="close" aria-label="Close" data-dismiss="cart"><span aria-hidden="true">&times</span></button>'+
					'</div>'+
					'<a href="/JiCoffee/view/product/detail/?serial='+ product.serial +'">'+
					'<div class="pull-left">'+
					'<img src="/JiCoffee/images/product/normal/'+ product.image +'" class="img-circle">'+
					'</div>'+
					'<h4>'+ product.name + 'x' + product.amount +'</h4>'+
					'<p>NT$ '+ product.totalprice +'</p>'+
					'</a>'+
					'</li>'
				);
			},

			removeItem: function(event) {
				event.preventDefault();

				var self = event.data.cart
				var $elem = $(self.elem);
				var index = $elem.find('button.close').index(this);
				
				self.defaults.product.splice(index, 1);
				$elem
					.find('span.amount').text(self.defaults.product.length).end()
					.find('ul.menu>li').eq(index).fadeOut();
					
				if ($.isEmptyObject(self.defaults.product)) {
					return sessionStorage.removeItem('product');
				}
		
				sessionStorage.setItem('product', JSON.stringify(self.defaults.product));
			},


		};	

		return Cart;
	}());


	$.fn.cart = function(options) {
		return new Cart(this, options).init();
	};


}(jQuery));