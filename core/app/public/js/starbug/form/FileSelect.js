define([
	"dojo",
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dijit/_WidgetBase",
	"dijit/_TemplatedMixin",
	"dijit/_WidgetsInTemplateMixin",
	"sb",
	"dojo/text!./templates/FileSelect.html",
	"put-selector/put",
	"dojo/on",
	"dojo/query",
	"dojo/dom-class",
	"starbug/form/Uploader"
], function (dojo, declare, lang, Widget, Templated, _WidgetsInTemplate, sb, template, put, on, query, domclass) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		files:[],
		templateString: template, //the template (./templates/FileSelect.html)
		widgetsInTemplate: true,
		input_name:'file',
		postCreate:function() {
			var self = this;
			
			this.input.name = this.input_name;
			
			if (self.files.length > 0) self.set_file(self.files[0]);

			//initialize the uploader
			this.uploader.url = WEBSITE_URL+'upload';	
			this.uploader.onBegin = lang.hitch(this, function() {
				self.set_status('loading');
			});
			this.uploader.onComplete = lang.hitch(this, 'onUpload');
			this.uploader.onAbort = lang.hitch(this, 'onCancelUpload');		
			
		},
		browse:function(){
			var win = dojo.global;
			var self = this;
			var modal;
			win.SetUrl=function(url, object){
				self.set_file(object);
			}
			modal = win.open(WEBSITE_URL+'admin/media?modal=true','media','modal,width=1020,height=600');
		},
		set_file:function(object) {
			this.set_status();
			var self = this;
			self.name.innerHTML = object.filename;
			self.input.value = object.id;
			self.image.innerHTML = '';
			if (object.mime_type.split('/')[0] == "image") {
				var img = put(self.image, 'img');
				img.src = WEBSITE_URL+'var/public/thumbnails/100x100a1/'+object.id+'_'+object.filename;
			}
		},
		set_status: function(value) {
			/**
			 * updates the status indicator
			 */
			if (!value) this.statusNode.innerHTML = '';
			else if (value == 'loading') {
				this.statusNode.innerHTML = '<span class="fa fa-spinner fa-spin fa-lg"></span>';
			}else this.statusNode.innerHTML = value;
		},
		onUpload: function(files) {
			/**
			 * upload handler. adds the file to the list once it has been uploaded.
			 */
			files[0].filename = files[0].original_name;
			this.set_file(files[0]);
		},
		onCancelUpload: function() {
			this.set_status();	
		}
	});
});
