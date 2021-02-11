define(["dojo/Deferred", "dojo/ready", "put-selector/put", "dojo/_base/config"], function(Deferred, ready, put, config) {
	if (!window.sb) {
		window.sb = {
			stores:{},
			errors:{},
			dialogs:{},
			hasLoadedTinyMCE:false,
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
			post: function(url, args, onsubmit) {
				if (typeof url == "object") {
					onsubmit = args;
					args = url;
					url = window.location.href;
				}
				if (url.substr(0, 4) != 'http') url = WEBSITE_URL+url;
				var form = put(window.document.body, 'form[method="post"]');
				form.style.display = 'none';
				form.setAttribute('action', url);
				if (onsubmit) form.setAttribute('onsubmit', onsubmit);
				for (var key in args) if (args.hasOwnProperty(key)) put(form, 'input[type=hidden]', {name:key, value:args[key]});
				var button = put(form, 'button[type=submit]', 'submit');
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
				var sb = this;
				var rt = window.document.getElementsByClassName("rich-text");
				var ed = window.document.getElementsByClassName("editable");
				if (rt.length > 0 || ed.length > 0) {
					if (this.hasLoadedTinyMCE) {
						window.tinymce.remove();
						sb.initTinyMCE(rt, ed);
						return;
					}
					var script = window.document.createElement('script');
					script.type = 'text/javascript';
					script.src = config.websiteUrl + 'libraries/tinymce/tinymce.min.js';
					var done = false;
					script.onload = script.onreadystatechange = function() {
						if ( !done && (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") ) {
							done = true;
							sb.initTinyMCE(rt, ed);
							// Handle memory leak in IE
							script.onload = script.onreadystatechange = null;
						}
					};
					window.document.head.appendChild(script);
					this.hasLoadedTinyMCE = true;
				}
			},
			initTinyMCE: function(rt, ed) {
				var tiny_mce_browser_callback = function(callback, value, meta){
					window.SetUrl=function(url,width,height,caption){
						if (meta.filetype == "file") {
							callback(url, {text: caption});
						} else if (meta.filetype == "image") {
							callback(url, {alt: caption});
						} else if (meta.filetype == "media") {
							callback(url);
						}
					};
					window.open(WEBSITE_URL+'admin/media?modal=true','media','modal,width=1020,height=600');
				};

				var tiny_options = {
					// General options
					plugins: [
							"advlist autolink autoresize lists link image charmap print preview hr anchor pagebreak",
							"searchreplace wordcount visualblocks visualchars code fullscreen charmap",
							"insertdatetime media nonbreaking save table directionality",
							"emoticons template paste save"
					],
					paste_auto_cleanup_on_paste : true,
					auto_cleanup_word: true,
					convert_urls: false,
					relative_urls: false,
					toolbar1: "undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | print preview",
					image_advtab: true,
					formats: {
						alignleft: {selector:'img', styles:{'margin':'0 15px 15px 0', 'float':'left'}},
						alignright: {selector:'img', styles:{'margin':'0 0 15px 15px', 'float':'right'}},
						btndefault: {inline:'a', classes:'btn btn-default', attributes:{href:'[uri:home]'}},
						btnprimary: {inline:'a', classes:'btn btn-primary', attributes:{href:'[uri:home]'}},
						btnsuccess: {inline:'a', classes:'btn btn-success', attributes:{href:'[uri:home]'}},
						btninfo: {inline:'a', classes:'btn btn-info', attributes:{href:'[uri:home]'}},
						btnwarning: {inline:'a', classes:'btn btn-warning', attributes:{href:'[uri:home]'}},
						btndanger: {inline:'a', classes:'btn btn-danger', attributes:{href:'[uri:home]'}}
					},
					style_formats_merge:true,
					style_formats: [
						{
							title:"Buttons", items: [
								{title:"Default Button", format:"btndefault"},
								{title:"Primary Button", format:"btnprimary"},
								{title:"Success Button", format:"btnsuccess"},
								{title:"Info Button", format:"btninfo"},
								{title:"Warning Button", format:"btnwarning"},
								{title:"Danger Button", format:"btndanger"}
							]
						}
					],
					file_picker_callback: tiny_mce_browser_callback
				};

				if (rt.length > 0) {
					tiny_options.selector = "textarea.rich-text";
					window.tinymce.init(tiny_options);
				}
				if (ed.length > 0) {
					tiny_options.selector = "div.editable";
					tiny_options.inline = true;
					tiny_options.save_enablewhendirty = true;
					tiny_options.toolbar1 += " save cancel";
					tiny_options.save_onsavecallback = function(editor) {
						var content = editor.getContent();
						var block_id = editor.bodyElement.getAttribute('data-block-id');
						sb.get('blocks').put({id:block_id, content:content}).then(function() {
							editor.bodyElement.blur();
						});
					};
					window.tinymce.init(tiny_options);
				}
			}
		};
	}
	ready(function() {
		window.sb.editable();
	});
	return window.sb;
});
