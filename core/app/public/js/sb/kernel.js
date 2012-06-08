define(['dojo', 'dojo/_base/config', 'dojo/_base/xhr'], function(dojo, config) {
	if (!dojo.global['sb']) {
		dojo.global['sb'] = {
			severTime: config.serverTime,
			notifier:config.notifier,
			stores:{},
			errors:{},
			require: function(module) {
				dojo['require']("starbug."+module);
			},
			star: function(str) {
				var starr = {};
				var pos = null;
				var keypairs = str.split('  ');
				for (var i in keypairs) {
					i = keypairs[i];
					if (-1 != (pos = i.indexOf(':'))) starr[i.substr(0, pos)] = i.substr(pos+1);
				}
				return starr;
			},
			xhr : function(url, args) {
				if (typeof url == "object") {
					args = url;
					url = window.location.href
				}
				if (url.substr(0, 4) != 'http') url = WEBSITE_URL+url;
				args.url = url;
				if (args.confirm && !confirm(args.confirm)) return;
				var xhr_object = {
					load: function(response, xhr) {
						args.action(response, args, xhr);
					}
				}
				dojo.mixin(xhr_object, args);
				if (args.method == "post") dojo.xhrPost(xhr_object);
				else dojo.xhrGet(xhr_object);
			},
			post: function(url, args, onsubmit) {
				if (typeof url == "object") {
					onsubmit = args;
					args = url;
					url = window.location.href;
				}
				if (url.substr(0, 4) != 'http') url = WEBSITE_URL+url;
				var form = dojo.create('form', {'method':'post', 'action':url, 'style': 'display:none'}, dojo.body());
				if (onsubmit) dojo.attr(form, 'onsubmit', onsubmit);
				for (var key in args) if (args.hasOwnProperty(key)) dojo.create('input', {'type':'hidden', 'name':key, 'value':args[key]}, form);
				var button = dojo.create('button', {'type':'submit', 'innerHTML':'submit'}, form);
				button.click();
			}
		};
		/*
		dojo.global.$_GET = [];
		var parts = String(document.location).split('?');
		if (parts[1]) dojo.global.$_GET = dojo.queryToObject(parts[1]);
		*/
	}
	return dojo.global.sb;
});
