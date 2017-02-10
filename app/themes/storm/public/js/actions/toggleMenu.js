define(["dojo/dom-class", "dojo/window"], function(domClass, win) {
	return function() {
		var body = document.body;
		var html = body.parentNode;
		var screen = win.getBox();
		if (!domClass.contains(body, "menu-on-top")){
			domClass.toggle(html, "hidden-menu-mobile-lock");
			domClass.toggle(body, "hidden-menu");
			domClass.remove(body, "minified");
		} else if (domClass.contains(body, "menu-on-top") && screen.w < 979 ) {
			domClass.toggle(html, "hidden-menu-mobile-lock");
			domClass.toggle(body, "hidden-menu");
			domClass.remove(body, "minified");
		}
	};
});
