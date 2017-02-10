define(["dojo/dom-class"], function(domClass) {
	return function() {
		var body = document.body;
		var html = body.parentNode;
		if (!domClass.contains(body, "menu-on-top")){
			domClass.toggle(body, "minified");
			domClass.remove(body, "hidden-menu");
			domClass.remove(html, "hidden-menu-mobile-lock");
		}
	};
});
