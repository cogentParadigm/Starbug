define(['dojo', 'dojo/_base/config', "dojo/_base/Deferred", 'dojo/_base/xhr'], function(dojo, config, Deferred) {
	if (!dojo.global['sb']) {
		dojo.global['sb'] = {
			severTime: config.serverTime,
			notifier:config.notifier,
			stores:{},
			errors:{},
			dialogs:{},
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
			},
			dialog: function(id, params, noshow) {
				/**
				 * create a dialog
				 */
				var promise = new Deferred();
				//create the new category dialog if it does not exist
				if (this.dialogs[id] == null) {
					var self = this;
					//dynamically require the dialog class
					require(["starbug/form/Dialog"], function(Dialog) {
						//create the new dialog, pointing to the terms creation form
						self.dialogs[id] = new Dialog(params);
						//start up and show the dialog
						self.dialogs[id].startup();
						if (!noshow) self.dialogs[id].show();
						promise.resolve(self.dialogs[id]);
					});
				} else {
					if (!noshow) self.dialogs[id].show();
					promise.resolve(this.dialogs[id]);
				}
				return promise;
			},
			editable: function() {
				var rt = dojo.global.document.getElementsByClassName("rich-text");
				if (rt.length > 0) {
					var script = dojo.global.document.createElement('script');
					script.type = 'text/javascript';
					script.src = '//tinymce.cachefly.net/4.0/tinymce.min.js';
					var done = false;
					script.onload = script.onreadystatechange = function() {
						if ( !done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") ) {
							done = true;
							console.log(dojo.global.tinymce);	
							var tiny_mce_browser_callback = function(field_name, url, type, win){
								window.SetUrl=function(url,width,height,caption){
								 var input_field = win.document.getElementById(field_name);
								 input_field.setAttribute('value', url);
								 if(caption){
										input_field.setAttribute('alt', caption);
								 }
								}
								window.open(WEBSITE_URL+'admin/media?modal=true','media','modal,width=800,height=600');
							};
							dojo.global.tinymce.init({
								// General options
								selector : "textarea.rich-text",
								theme : "modern",
								plugins: [
										"advlist autolink autoresize textcolor lists link image charmap print preview hr anchor pagebreak",
										"searchreplace wordcount visualblocks visualchars code fullscreen charmap",
										"insertdatetime media nonbreaking save table contextmenu directionality",
										"emoticons template paste"
								],

								toolbar1: "undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | print preview",
								image_advtab: true,
								file_browser_callback: tiny_mce_browser_callback
							});

							// Handle memory leak in IE
							script.onload = script.onreadystatechange = null;
						}
					};
					dojo.global.document.head.appendChild(script);
				}
			}
		};
		/*
		dojo.global.$_GET = [];
		var parts = String(document.location).split('?');
		if (parts[1]) dojo.global.$_GET = dojo.queryToObject(parts[1]);
		*/
	}
	dojo.global.sb.editable();
	return dojo.global.sb;
});
