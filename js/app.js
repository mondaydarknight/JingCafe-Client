(function() {

	this.Url = (function() {
		var getUrlParameter = function() {
	        var resource = {};

	        window.location.search.substr(1).split("&").forEach(function(item) {
	            resource[item.split("=")[0]] = item.split("=")[1]
	        });

	        return resource;
	    };

	    return {
	        getUrlParameter: getUrlParameter()
	    }
	}());

	this.App = (function() {

		App.prototype.defaults = {
			url: '/JiCoffee/app/server.php',
			type: 'POST',
			dataType: 'json'
		};

		function App() {
			this.init();
		}

		App.prototype.init = function() {
			amplify.request.define('app', 'ajax', this.defaults);

			amplify.subscribe('app', this.loadComponent);
			amplify.subscribe('app', $.proxy(this.user, this));
		};

		App.prototype.httpGet = function() {
			this.defaults.type = 'GET';
			amplify.request.define('get', 'ajax', this.defaults);
		};
		
		App.prototype.loadComponent = function() {
			$('div[data-include]').each(function() {
				$(this).load('/JiCoffee/view/component/' + $(this).data('include') + '.html');
			});
		};

		App.prototype.user = function(result) {
			if (result.user === 'Member') {
                $('#user').addClass('dropdown user user-menu').html(result.userMenu)
                	.find('#logout').on('click.logout', this.userLogout);
               	
               	setTimeout(function() {
               		$('ul.navbar-nav', 'div[data-include="menu"]').append(result.orderMenu);
               	}, 100);
				
			} else {
				// Visitor mode
				$('#user').one('click.initLogin', function(event) {
					event.preventDefault();
					new Login($('#login'));
				});
				
			}
		};

		App.prototype.userLogout = function(event) {
			event.preventDefault();

			amplify.request('app', {operation: 'logout'}).done(function() {
				setTimeout(function() {
					window.location.replace('/JiCoffee/');
				}, 10);
			});
		};

		App.cart = function() {
			var products = [];
			if (sessionStorage.getItem('product') !== null) {
				products = JSON.parse(sessionStorage.getItem('product'));
			}

			$('#cart').cart({product: products});
		};

		App.clearProductRecord = function() {
			sessionStorage.removeItem('product');
		};

		App.goOrderLobby = function() {
			setTimeout(function() {
				window.location.replace('/JiCoffee/view/order/view');
			}, 1500);
		}

		App.error = function() {
			setTimeout(function() {
				window.location.replace('/JiCoffee/view/404.html');
			}, 10);
		};

		return App;
	}());


}).call(this);