(function() {

	var Login;

	this.Login = (function() {

		Login.prototype.defaults = {
			index: '/JiCoffee/view/component/login.html',
			section: null,
			target: 'signIn'
		};

		function Login($loginSection) {
			this.defaults.section = $loginSection;
			this.init();
		}

		Login.prototype.init = function() {
			this.defaults.section.load(this.defaults.index)
				.on('click.switchInterface', 'button.switch', $.proxy(this.setAnimate, this))
				.on('submit.formData', 'form', $.proxy(this.submitForm, this))
				.on('shown.bs.modal', '#login-modal', $.proxy(this.autoFocus, this));

			setTimeout($.proxy(function() {
				this.defaults.section.find('#login-modal').modal('show');
			}, this), 100);
		};

		Login.prototype.setAnimate = function(event) {
			event.preventDefault();
			this.defaults.target = $(event.target).data('purpose');

			var $oldForm = this.defaults.section.find('form').filter(':visible');
			var $newForm = this.defaults.section.find('#' + this.defaults.target + '-form');

			$oldForm.parent().animate({height: $newForm.height()}, 200, function() {
	            $oldForm.hide();
	            $newForm.fadeToggle();
	        });
		};

		Login.prototype.submitForm = function(event) {
			event.preventDefault();
			
			amplify.request.define('login', 'ajax', {
				url: '/JiCoffee/app/server.php',
				type: 'POST',
				dataType: 'json'
			});
			
			this[this.defaults.target].apply(this, [$(event.target)]);
		};

		Login.prototype.signIn = function($form) {
			var authentication = {operation: 'login'};

			$.extend(authentication, this.serializeParams($form));

			amplify.request('login', authentication).done($.proxy(this.signInRespond, this));
		};

		Login.prototype.signInRespond = function(result, status) {
			var $form = this.defaults.section.find('#signIn-form');

			this.clearInputError($form);
		
			console.log(result);

			switch(result) {
				case 'accountFail':
					this.inputError($form.find('#account'));
					break;

				case 'passwordFail':
					this.inputError($form.find('#password'));
					break;

				case 'detectDeviceFail':
					$form.find('#login-msg').text('無法辨識裝置');
					break;

				case 'success':
					$form.find('#login-msg').text('登入成功').find('i').removeClass('fa-chevron-right').addClass('fa-check');
					setTimeout(function() {
						window.location.reload();
					}, 800);
					break;
			}
		};

		Login.prototype.clearInputError = function($form) {
			$form.find('div.form-group').removeClass('has-error').find('span.validation').hide();
		};

		Login.prototype.inputError = function($element) {
			$element.focus().next().show('fast').end().parent().addClass('has-error');
		};

		Login.prototype.serializeParams = function($form) {
			var params = {};
			$form.serializeArray().map(function(item) {
				params[item.name] = item.value;
			});

			return params;
		};

		Login.prototype.lost = function($form) {

		};

		Login.prototype.lostRespond = function(result) {

		};

		Login.prototype.register = function($form) {
			var registerParams = {operation: 'register'};
			var $password = $form.find('#register-password');
			var $passwordAgain = $form.find('#register-password-again');

			this.clearInputError($form);

			if ($password.val().length < 6) {
				return this.inputError($password);
			}

			if ($password.val() !== $passwordAgain.val()) {
				return this.inputError($passwordAgain);
			}

			$.extend(registerParams, this.serializeParams($form));
			amplify.request('login', registerParams).done($.proxy(this.registerRespond, this));
		};

		Login.prototype.registerRespond = function(result, status) {
			var $form = this.defaults.section.find('#register-form');
			var $alertMsg = $form.find('#register-msg');

			if (result.process === true) {
				$alertMsg.text('註冊成功').find('i').removeClass('fa-chevron-right').addClass('fa-check');
				setTimeout($.proxy(function() {
					this.defaults.section.find('#account').val(result.email).end().find('#password').val(result.password);
					$form.find('button[data-purpose="signIn"]').trigger('click.switchInterface');		
				}, this), 1000);
			} else if (result.warning) {
				this.inputError($form.find('input[name="email"]'));
			} else if (result.error) {
				$alertMsg.text('發生錯誤');
			}
		};

		// Login.prototype.alertDanger = function($alert, type, text) {
		// 	$alert.text(text).parent().removeClass('alert-warning').addClass('alert-danger');
		// }

		Login.prototype.autoFocus = function(event) {
			$(event.target).find('form input').first().focus();
		};

		return Login;
	})();

}).call(this);