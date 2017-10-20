(function() {

	this.Transaction = (function() {

		var Transaction = function($personelInfo, $productList, $section, $deliverInfo, $pay, $submit, captcha) {
			this.personelInfo = $personelInfo;
			this.productList = $productList;
			this.section = $section; 
			this.deliverInfo = $deliverInfo;
			this.pay = $pay;
			this.submit = $submit;
			this.captcha = captcha;
			this.clickStatus = false;
			this.init();
		};

		Transaction.prototype.init = function() {
			this.personelInfo
				.on('keyup.onlyNumber', '#phone', $.proxy(this.onlyNumber, this))
				.on('click.memberRecordSetting', 'input.record', $.proxy(this.memberRecordSetting, this))
				.on('submit.validate', $.proxy(this.memberValidate, this));

			this.productList
				.one('displayProductList', $.proxy(this.displayProductList, this))
				.on('click.minus', 'a.minus', {list: this.productList}, this.productMinus)
				.on('click.plus', 'a.plus', {list: this.productList}, this.productPlus);
			
			this.section
				.on('shown.bs.modal', $.proxy(this.showPayModal, this))
				.on('click.checkout', 'button.checkout', $.proxy(this.confirmPayModal, this));

			this.deliverInfo
				.on('click.changeDeliver', 'input[name="deliver-type"]', {deliverInfo: this.deliverInfo, feeSettle: this.productList.next()}, this.changeDeliver);

			this.submit
				.on('click.pay', $.proxy(this.paySubmit, this));
		};	

		Transaction.prototype.createTransaction = function(checkout) {
			var command = $.extend({operation: 'sendOrder'}, checkout);
			this.transactionProcess(command);
			App.clearProductRecord();
		};

		Transaction.prototype.alterTransaction = function(checkout) {
			var command = $.extend({operation: 'alterOrder', transactionid: this.productList.data('transactionid')}, checkout);
			this.transactionProcess(command);
		};

		Transaction.prototype.transactionProcess = function(command) {
			amplify.request('app', command).done($.proxy(this.respond, this));
		};

		Transaction.prototype.respond = function(result, status) {
			
			if (result.error) {
				swal({
				  	title: result.error,
				  	text: '系統將返回訂單頁面',
				  	type: "warning",
				  	closeOnConfirm: false
				});

				setTimeout(function() {
					window.location.replace('/JiCoffee/view/order/view');
				}, 1500);

				return;
			}

			swal({
				title: result,
				type: 'success'
			});

			setTimeout(function() {
				window.location.replace('/JiCoffee/view/order/view');
			}, 1000);
		};

		Transaction.prototype.serverProductLoad = function(result) {
			
			if (result === 'error') {
				return App.error();
			}

			this.listProduct(result.product);
			this.productList.data('transactionid', result.id).find('td.input-group')
				.prepend('<span class="input-group-btn set"><a href="#" class="btn minus"><i class="fa fa-minus"></i></a></span>')
				.append('<span class="set"><a href="#" class="btn plus"><i class="fa fa-plus"></i></a></span>');
		};

		Transaction.prototype.productMinus = function(event) {
			event.preventDefault();

			var $productList = $(event.data.list);
			var index = $productList.find('a.minus').index(this);
			var $amount = $productList.find('span.amount').eq(index);
			
			if ($amount.text() > 1) {
				$amount.text(parseInt($amount.text()) - 1);
				Transaction.priceUpdate($productList, index, parseInt($amount.text()));
			}
		};

		Transaction.prototype.productPlus = function(event) {
			event.preventDefault();

			var $productList = $(event.data.list);
			var index = $productList.find('a.plus').index(this);
			var $amount = $productList.find('span.amount').eq(index);

			if ($amount.text() < 20) {
				$amount.text(parseInt($amount.text()) + 1);
				Transaction.priceUpdate($productList, index, parseInt($amount.text()));
			}
		};

		Transaction.prototype.sessionProductLoad = function() {
			if (sessionStorage.getItem('product') !== null) {
				var products = JSON.parse(sessionStorage.getItem('product'));
				
				console.log(products);	
				this.listProduct(products);
				return;
			}

			swal('尚未訂購產品，無法結帳');
			setTimeout(function() {
				window.location.replace('/JiCoffee/view/product/?category=bean');
			}, 1500);
		};

		Transaction.prototype.listProduct = function(products) {
			this.productList.trigger('displayProductList', [products]);
		};

		Transaction.prototype.displayProductList = function(event, products) {
			event.preventDefault();
			var list = [];
			var total = 0;
				
			$.each(products, function(index, product) {
				list.push(
					'<tr>'+
					'<td><img src="/JiCoffee/images/product/normal/'+ product.image +'" class="img-thumbnail img-small"></td>'+
					'<td class="productName"><a href="/JiCoffee/view/product/detail/?serial='+ product.serial +'">'+ product.name +'</a></td>'+
					'<td>NT$ <span class="price">'+ product.price +'</span></td>'+
					'<td class="input-group"><span class="amount" data-productid="'+product.productid+'">'+ product.amount +'</span></td>'+
					'<td>NT$ <span class="totalprice">'+ product.totalprice +'</span></td>'+
					'</tr>'
				);
				
				total += parseInt(product.totalprice);
			});

			this.productList.find('tbody').html(list).end()
				.next().find('span.originPrice, span.totalPrice').text(total);
		};

		Transaction.prototype.memberRecord = function(result) {
			if (result.user === 'Member') {
				return this.personelInfo
					.prepend(result.record)
					.data('username', result.username)
					.data('email', result.email)
					.data('phone', result.phone)
					.find('input.record').trigger('click.memberRecordSetting');
			}

			$('div.box-tools').append(result.box).on('click.login', 'button.login', function(e) {
				e.preventDefault();
				$('#user').trigger('click.initLogin');
			});
		};

		Transaction.prototype.deliverRecord = function(result) {
			this.deliverInfo.html(result.deliver);
		};

		Transaction.prototype.payRecord = function(result) {
			this.pay.find('input[name="account"]').val(result.bankaccount);
		};
		
		Transaction.prototype.showPayModal = function(event) {
			event.preventDefault();
		
			var $modal = this.section.find('#pay-modal');
			var $fee = this.productList.next();
			var deliverType = this.deliverInfo.find('div.alert-info span.frb-title').text();
			var atm = this.pay.find('input').eq(0).val();

			this.personelInfo.serializeArray().map(function(item) {
				$modal.find('span.' + item.name).text(item.value);
			});

			$modal
				.find('span.deliver').text(deliverType).end()
				.find('span.bankAccount').text(atm).end()
				.find('span.deliverPrice').text($fee.find('span.deliverPrice').text()).end()
				.find('span.totalPrice').text($fee.find('span.totalPrice').text()).end()
				.find('div.list').html(this.productList.clone()).find('span.set').remove();
		};

		Transaction.prototype.confirmPayModal = function(event) {
			event.preventDefault();

			var checkout = {productId: [], productAmount: [], productName: []};
			this.section.find('img.hide').removeClass('hide');
			
			checkout['bankAccount'] = $('input', '#atm').eq(0).val();
			checkout['total'] = this.productList.next().find('span.totalPrice').text();
			checkout['deliverId'] = this.deliverInfo.find('div.alert-info input[name="deliver-type"]').data('deliver');
			checkout['message'] = this.productList.next().next().find('textarea').val();

			this.personelInfo.serializeArray().map(function(item) {
				checkout[item.name] = item.value;
			});
			
			this.productList.find('span.amount').each(function(index) {
				checkout.productName.push($(this).closest('tr').find('td.productName').text());
				checkout.productId.push($(this).data('productid'));
				checkout.productAmount.push($(this).text());
			});

			amplify.publish('pay', checkout);
		};

		Transaction.prototype.onlyNumber = function(event) {
			event.preventDefault();
			
			var $target = $(event.target);
			$target.val($target.val().replace(/[^0-9]/g, ''));
		};

		Transaction.prototype.memberFillRecord = function() {
			var documentRecord = this.personelInfo.data();
			this.personelInfo
				.find('#username').val(documentRecord.username).end()
				.find('#email').val(documentRecord.email).end()
				.find('#phone').val(documentRecord.phone);
		};

		Transaction.prototype.memberClearRecord = function() {
			this.personelInfo.find('#username, #email, #phone').val('');
		};

		Transaction.prototype.memberRecordSetting = function() {
			this.clickStatus = !this.clickStatus;
			return this.clickStatus ? this.memberFillRecord() : this.memberClearRecord();
		};

		Transaction.prototype.changeDeliver = function(event) {
			var $deliverInfo = $(event.data.deliverInfo);
			var $feeSettle = $(event.data.feeSettle);
			var index = $deliverInfo.find('input[name="deliver-type"]').index(this);
			var fee = parseInt($deliverInfo.find(this).nextAll('span.pull-right').find('span.fee').text());

			$deliverInfo.find('div.alert').removeClass('alert-info').addClass('alert-default')
				.eq(index).removeClass('alert-default').addClass('alert-info');

			$feeSettle.find('span.deliverPrice').text(fee).end()
				.find('span.totalPrice').text(parseInt($feeSettle.find('span.originPrice').text()) + fee);

		};

		Transaction.prototype.memberValidate = function(event) {
			event.preventDefault();

			if (this.captcha.validateCaptcha()) {
				swal('儲存成功');
				return $(event.target).data('verify', true).find('button[rel="tooltip"]').tooltip('hide');
			}
		};

		Transaction.prototype.paySubmit = function(event) {
			event.preventDefault();

			var $self = $(event.target);

			if (!this.personelInfo.data('verify')) {
				$self.add(this.personelInfo.find('#save')).tooltip('show');
				return false;
			}

			var $account = this.pay.find('input[name="account"]');

			$account.parent().removeClass('has-error').end().next().hide();

			if ($account.eq(0).val().length !== 5) {
				this.inputError($account.eq(0));
				$self.tooltip('show');
				return false;
			} else if ($account.eq(0).val() !== $account.eq(1).val()) {
				this.inputError($account.eq(1));
				$self.tooltip('show');
				return false;
			}

			$self.tooltip('hide');
			this.section.find('#pay-modal').modal('show');
		};

		Transaction.prototype.inputError = function($element) {
			$element.parent().addClass('has-error').end().next().show('fast');
		};

		Transaction.priceUpdate = function($productList, index, amount) {
			var money = 0;
			var $price = $productList.find('span.price').eq(index);
			var $totalPrice = $productList.find('span.totalprice');
			var $settle = $productList.next();

			$totalPrice.eq(index).text(parseInt($price.text()) * amount).end()
				.each(function(index, element) {
				money += parseInt($(element).text());		
			});
			
			$settle.find('span.originPrice').text(money).end()
				.find('span.totalPrice').text(money + parseInt($settle.find('span.deliverPrice').text()));
		};

		return Transaction;
	})();

}).call(this);
