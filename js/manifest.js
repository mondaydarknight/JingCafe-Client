
var manifest = [];

var meta = [];

var generateMeta = function(ajaxString) {
	var headTarget = document.getElementsByTagName('head')[0];

	manifest = JSON.parse(ajaxString);
    
	
	$.each(manifest.meta, function(i, meta) {

		if (meta.hasOwnProperty('name')) {
			var metaTag = document.createElement('meta');

			metaTag.name = meta.name;
			metaTag.content = meta.content;

			headTarget.appendChild(metaTag);			
		} else if (meta.hasOwnProperty('icons')) {
			$.each(meta.icons, function(i, icon) {
				var linkTag = document.createElement('link');

				linkTag.rel = icon.rel;
				linkTag.sizes = icon.sizes;
				linkTag.href = icon.href;
				linkTag.type = icon.type;

				headTarget.appendChild(linkTag);
			});
		}
	});

	// console.log(headTarget);
};

var manifestAjax = function(url) {
	if (!window.XMLHttpRequest) return;
	var requestUrl;
	var preg = /^https?:\/\//i;
	var ajax = new XMLHttpRequest();
	
	preg.test(url) ? requestUrl = url : requestUrl = window.location.hostname + url;

	ajax.onreadystatechange = function() {
		if (ajax.readyState === 4 && ajax.status === 200)
			generateMeta(ajax.responseText);
	};

	ajax.open("GET", requestUrl, true);
	ajax.send();

}

var collectManifest = function() {
	var links = document.getElementsByTagName('link');
	
	for (var i = 0; i < links.length; i++) {
		if (links[i].rel && links[i].rel === 'manifest') {
			manifestAjax(links[i].href);
		}
	}
};


var manifestInit = (function() {
	collectManifest();
})();