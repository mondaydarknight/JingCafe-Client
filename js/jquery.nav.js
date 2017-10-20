


;(function($, window, document, undefined) {

	var OnePageNav = function(elem, options) {
		this.elem = elem;
		this.$elem = $(elem);
		this.options = options;
		this.metadata = this.$elem.data('plugin-options');
		this.$nav = this.$elem.find('a');
		this.$win = $(window);
		this.sections = {};
		this.didScroll = false;
		this.$doc = $(document);
		this.docHeight = this.$doc.height();
	}

	OnePageNav.prototype = {
		defaults: {
			currentClass: 'current',
			changeHash: false,
			easing: 'swing',
			filter: ':not(.external)',
			scrollSpeed: 750,
			scrollOffset: 0,
			scrollThreshold: 0.5,
			begin: false,
			end: false,
			scrollChange: false
		},

		init: function() {
			var self = this;
		
			self.config = $.extend({}, self.defaults, self.options, self.metadata);	

			// filter any links out of nav
			if (self.config.filter !== '') {
				self.$nav = self.$nav.filter(self.config.filter);
			}

			// handle clicks on the nav
			self.$nav.on('click.onePageNav', $.proxy(self.handleClick, this));

			// get section positions
			self.getPositions();

			// Handle scroll change
			self.bindInterval();

			// update the positions on resize too
			self.$win.on('resize.onePageNav', $.proxy(self.getPositions, self));

			return this;
		},

		adjustNav: function(self, $parent) {
			self.$elem.find('.' + self.config.currentClass).removeClass(self.config.currentClass);
			$parent.addClass(self.config.currentClass);
		},

		bindInterval: function() {
			var self = this;
			var docHeight;

			self.$win.on('scroll.onePageNav', function() {
				self.didScroll = true;
			});

			self.t = setInterval(function() {
				docHeight = self.$doc.height();

				if (self.didScroll) {
					self.didScroll = false;
					self.scrollChange();
				}

				if (docHeight !== self.docHeight) {
					self.docHeight = docHeight;
					self.getPositions();
				}

			}, 250);
		},

		getHash: function($link) {
			return $link.attr('href').split('#')[1];
		},

		getPositions: function() {
			var self = this;
			var linkHref;
			var topPosition;
			var $target;

			// put each nav link position into sections
			self.$nav.each(function() {
				linkHref = self.getHash($(this));
				$target = $('#' + linkHref);

				if ($target.length) {
					topPosition = $target.offset().top;
					self.sections[linkHref] = Math.round(topPosition) - self.config.scrollOffset;
				}
			});
		},

		getSection: function(windowPosition) {
			var returnValue = null;
			var windowHeight = Math.round(this.$win.height() * this.config.scrollThreshold);

			for (var section in this.sections) {
				if ((this.sections[section] - windowHeight) < windowPosition) {
					returnValue = section;
				}
			}

			return returnValue;
		},

		handleClick: function(e) {
			var self = this;
			var $link = $(e.currentTarget);
			var $parent = $link.parent();
			var location = '#' + self.getHash($link);

			if (!$parent.hasClass(self.config.currentClass)) {
				// start callback
				if (self.config.begin) {
					self.config.begin();
				}

				// change the highlighted nav item
				self.adjustNav(self, $parent);

				self.unbindInterval();

				// using jQuery plugin scrollTo
				$.scrollTo(location, self.config.scrollSpeed, {
					axis: 'y',
					easing: self.config.easing,
					offset: {
						top: -self.config.scrollOffset
					},
					onAfter: function() {
						if (self.config.changeHash) {
							window.location.hash = location;
						}

						self.bindInterval();

						if (self.config.end) {
							self.config.end();
						}
					}
				});
			}

			e.preventDefault();
		},

		scrollChange: function() {
			var windowTop = this.$win.scrollTop();
			var position = this.getSection(windowTop);
			var $parent;

			if (position !== null) {
				// get target a parent link
				$parent = this.$elem.find('a[href$="#' + position + '"]').parent();

				if (!$parent.hasClass(this.config.currentClass)) {
					this.adjustNav(this, $parent);

					if (this.config.scrollChange) {
						this.config.scrollChange($parent);
					}
				}
			}
		},

		unbindInterval: function() {
			clearInterval(this.t);
			this.$win.off('scroll.onePageNav');
		}
	};

	OnePageNav.defaults = OnePageNav.prototype.defaults;

	$.fn.onePageNav = function(options) {
		return this.each(function() {
			new OnePageNav(this, options).init();
		});
	};


}(jQuery, window, document));

