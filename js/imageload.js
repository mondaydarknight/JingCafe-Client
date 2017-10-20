
(function (window) {
	'use strict';

	var $ = window.jQuery;
	var console = window.console;
	var hasConsole = typeof console !== 'undefined';

	function extend(a, b) {
		for (var prop in b) {
			a[prop] = b[prop];
		}
		return a;
	}

	var objToString = Object.prototype.toString;

	function isArray(obj) {
		return objToString.call(obj) === '[object Array]';
	}

	function makeArray(obj) {
		var array = [];

		if (isArray(obj)) {
			array = obj;
		} else if (typeof obj.length === 'number') {
			for (var i=0, len = obj.length; i< len; i++) {
				array.push(obj[i]);
			}
		} else {
			array.push(obj);
		}
		return array;
	}

	function defineImagesLoaded(EventEmitter, eventie) {
		var cache = {};
		var ImageLoad = function(elem, options, onAlways) {
			var self = this;
			if (!(this instanceof ImageLoad)) {
				return new ImageLoad(elem, options);
			}

			if (typeof elem === 'string') {
				elem = document.querySelectorAll(elem);
			}

			if (typeof options === 'function') {
				onAlways = options;
			} else {
				extend(this.options, options);
			}

			if (onAlways) {
				this.on('always', onAlways);
			}

			this.getImages();

			if ($) {
				this.jqDeferred = new $.Deferred();
			}

			setTimeout(function() {
				this.check();
			});
		};

		ImageLoad.prototype = new EventEmitter();
		ImageLoad.prototype = {
			options: {},
			getImages: function() {
				this.images = [];

				for (var i=0, len=this.elements.length; i<len; i++) {
					var elem = this.elements[i];
					var childElement = elem.querySelectorAll('img');

					if (elem.nodeName === 'img') {
						this.addImage(elem);						
					}

					for (var j=0, jLen=childElement.length; j<jLen; j++) {
						var img = childElement[j];
						this.addImage(img);
					}
				}
			},
			addImage: function(img) {
				var loadingImage = new LoadingImage(img);
				this.images.push(loadingImage);
			},
			check: function() {
				var self = this;
				var checkedCount = 0;
				var length = this.images.length;
				this.hasAnyBroken = false;

				var onConfirm = function(image, message) {
					if (self.options.debug && hasConsole) {
						console.log('confirm', image, message);
					}

					self.progress(image);
					checkedCount++;
					if (checkedCount === length) {
						self.complete();
					}

					return true;
				};

				if (!length) {
					this.complete();
					return;
				}

				for (var i=0; i<length; i++) {
					var loadingImage = this.images[i];
					loadingImage.on('confirm', onConfirm);
					loadingImage.check();
				}
			},
			progress: function(image) {
				this.hasAnyBroken = this.hasAnyBroken || !image.isLoaded;
				this.emit('progress', this, image);
				if (this.jqDeferred) {
					this.jqDeferred.notify(this, image);
				}
			},
			complete: function() {
				var eventName = this.hasAnyBroken ? 'fail' : 'done';

				this.isComplete = true;
				this.emit(eventName, this);
				this.emit('always', this);
				
				if (this.jqDeferred) {
					var jqMethod = this.hasAnyBroken ? 'reject' : 'resolve';
					this.jqDeferred[jqMethod](this);
				}
			}
		};

		var LoadingImage = function(img) {
			this.img = img;
		};

		LoadingImage.prototype = new EventEmitter();
		LoadingImage.prototype = {
			check: function() {
				var cached = cache[this.img.src];
				var proxyImage = this.proxyImage = new Image();

				if (cached) {
					this.useCashed(cached);
					return;
				}

				cached[this.img.src] = this;

				if (this.img.complete && this.img.naturalWidth !== undefined) {
					this.confirm(this.img.naturalWidth !== 0, 'naturalWidth');
					return;
				}

				eventie.emit(proxyImage, 'load', this);
				eventie.emit(proxyImage, 'error', this);
				proxyImage.src = this.img.src;
			},
			useCashed: function(cached) {
				if (cached.isConfirmed) {
					this.confirm(cached.isLoaded, 'cached was confirmed');
				} else {
					var self = this;
					cached.on('confirm', function(image) {
						self.confirm(image.isLoaded, 'cache emitted confirmed');
						return true;
					});
				}
			},
			confirm: function(isLoaded, message) {
				this.isConfirmed = true;
				this.isLoaded = isLoaded;
				this.emit('confirm', this, message);
			},
			handleEvent: function(event) {
				var method = 'on' + event.type;
				if (this[method]) {
					this[method](event);
				}
			},
			onload: function() {
				this.confirm(true, 'onload');
				this.unbindProxyEvents();
			},
			onerror: function() {
				this.confirm(false, 'onerror');
				this.unbindProxyEvents();
			},
			unbindProxyEvents: function() {
				eventie.unbind(this.proxyImage, 'load', this);
				eventie.unbind(this.proxyImage, 'error', this);
			}

		};

		if ($) {
			$.fn.imagesLoad = function(options, callback) {
				var instance = new ImageLoad(this, options, callback);
				return instance.jqDeferred.promise($(this));
			};
		}

		return ImageLoad;
	}

	if (typeof define === 'function' && define.amd) {
		define([
			'eventEmitter',
			'eventie'
		],
		defineImagesLoaded);
	} else {
		window.imagesLoad = defineImagesLoaded(
			window.EventEmitter,
			window.eventie
		);
	}

}(window));

