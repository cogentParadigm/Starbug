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
	"dojo/_base/Deferred",
	"dstore/Memory",
	"dstore/Trackable",
	"dojo/ready",
	"starbug/form/Uploader",
	"starbug/grid/MemoryGrid",
	"starbug/grid/columns/filesize",
	"starbug/grid/columns/handle",
	"starbug/grid/columns/html",
	"starbug/form/columns/FileSelectOptions"
], function (dojo, declare, lang, Widget, Templated, _WidgetsInTemplate, sb, template, put, on, query, domclass, Deferred, Memory, Trackable, ready) {
	var TrackableMemory = declare([Memory, Trackable]);
	return declare([Widget, Templated, _WidgetsInTemplate], {
		files:[],
		templateString: template, //the template (./templates/FileSelect.html)
		widgetsInTemplate: true,
		input_name:'file',
		size:1,
		store:null,
		postCreate:function() {
			var self = this;
			this.store = new TrackableMemory({data: []});
			// initialize hidden input
			this.input.name = this.input_name;

		},
		startup:function() {
			var self = this;
			this.uploader.startup();
			this.grid.editor = self;
			//initialize the uploader
			this.uploader.url = WEBSITE_URL+'upload';
			this.uploader.onBegin = lang.hitch(this, function() {
				self.set_status('loading');
			});
			this.uploader.onComplete = lang.hitch(this, 'onUpload');
			this.uploader.onAbort = lang.hitch(this, 'onCancelUpload');
			this.grid.set('collection', this.store);
			setTimeout(function() {
				if (self.files.length > 0) self.add(self.files);
			}, 100);
		},
		browse:function(){
			var win = dojo.global;
			var self = this;
			var modal;
			win.SetUrl=function(url, object){
				self.add([object]);
			};
			modal = win.open(WEBSITE_URL+'admin/media?modal=true','media','modal,width=1020,height=600');
		},
		add:function(files) {
			var target_size = this.store.data.length + files.length;
			if (this.size == 0) {
				//unlimited
			} else if (this.size == 1) {
				if (this.store.data.length) this.store.remove(this.store.data[0].id);
			} else if (target_size == this.size) {
				this.controls.style.display = 'none';
			} else if (target_size > this.size) {
				alert("You have reached the limit.");
				return;
			} else {
				this.controls.style.display = 'block';
			}
			this.set_status();
			var self = this;
			for (var i in files) {
				var object = files[i];
				if (files[i].filename[0] != "<") {
					var full_path = WEBSITE_URL+'var/public/thumbnails/100x100a1/'+object.id+'_'+object.filename;
					var div = put('div');
					if (object.mime_type.split('/')[0] == "image") {
						var img = put(div, 'img');
						img.src = full_path;
					}
					put(div, 'a.filename[href="'+full_path+'"][target="_blank"]', object.filename);
					files[i].filename = div.innerHTML;
				}
				this.store.put(files[i]);
			}
			if (files.length > 0) {
				self.gridNode.style.display = 'block';
			} else {
				self.gridNode.style.display = 'none';
			}
			self.refresh();
			dojo.global.dispatchEvent(new Event('resize'));
		},
		remove: function(file_id) {
			this.store.remove(file_id);
			this.refresh();
		},
		refresh: function() {
			this.grid.refresh();
			this.grid.renderArray(this.store.data);
			this.grid.set('collection', this.store);
			var ids = [];
			var items = this.store.data;
			for (var i = 0;i<items.length;i++) ids.push(this.store.getIdentity(items[i]));
			ids.push("-~");
			this.input.value = ids.join(',');
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
			for (var i = 0;i < files.length;i++) {
				files[i].filename = files[i].original_name;
			}
			this.add(files);
		},
		onCancelUpload: function() {
			this.set_status();
		}
	});
});
