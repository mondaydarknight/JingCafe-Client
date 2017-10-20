(function($) {
	$(function() {
		
		var Lang = function() {
			var self = this;
			var i18n = {
				en: function() {
					self.translate.apply(this);
				},
				'zh-TW': function() {
					self.translate.apply(this);
				}
			};

			var setLanguage = function() {
				$(this).addClass('active');
				var language = Array.prototype.shift.call(arguments);
				i18n[language].apply($('.lang'));
			};

			return {
				setLanguage: setLanguage
			}

		};

		Lang.prototype.nodeText = function() {
			return this.nodeType = Node.TEXT_NODE;
		};

		Lang.prototype.translate = function() {

			// var word = Array.prototype.shift.call(arguments);
			// this.each(function(i) {
			// 	console.log(wod[i]);
			// 	// $(this).contents().filter(Lang.nodeText).get(0).nodeValue = word[i];
			// });
		};

		var Lang = new Lang;
		var $lang = $('#language');
		
		$lang.on('click', 'a', function() {
			Lang.setLanguage.apply(this, [$(this).data('lang')]);
		});

	})
	
}(jQuery));