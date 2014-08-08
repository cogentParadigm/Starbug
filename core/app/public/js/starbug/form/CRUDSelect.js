define([
	"dojo",
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dijit/_WidgetBase",
	"dijit/_TemplatedMixin",
	"dijit/_WidgetsInTemplateMixin",
	"sb",
	"dojo/text!./templates/CRUDSelect.html",
	"put-selector/put",
	"dojo/on",
	"dojo/query",
	"dojo/dom-class",
	"dojo/_base/Deferred",
	"starbug/store/Memory",
	"dojo/store/Observable",
	"dojo/store/util/QueryResults",
	"dojo/ready",
	"starbug/form/Dialog",
	"starbug/grid/MemoryGrid",
	"starbug/grid/columns/handle",
	"starbug/grid/columns/html",
	"starbug/grid/columns/options"
], function (dojo, declare, lang, Widget, Templated, _WidgetsInTemplate, sb, template, put, on, query, domclass, Deferred, Memory, Observable, QueryResults, ready, Dialog) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		value:[],
		templateString: template, //the template (./templates/FileSelect.html)
		widgetsInTemplate: true,
		input_name:'file',
		size:1,
		store:null,
		dialog:null,
		postCreate:function() {
			var self = this;
			this.store = Observable(new Memory({data: []}));
			this.dialog = new Dialog({url:"admin/uris_images/", callback:function(data) {
				var object_id = query('input[name="uris_images[id]"]').attr('value')[0];
				sb.get('uris_images', 'select').query({'id':object_id}).then(function(data) {
					self.add(data);
				});
			}});
			this.dialog.startup();
			self.grid.editor = self;
			ready(function() {
				self.grid.set('store', self.store);
			});
			
			// initialize hidden input
			this.input.name = this.input_name;
			
			if (self.value.length > 0) {
				sb.get('uris_images', 'select').query({'id':self.value.join(',')}).then(function(data) {
					self.add(data);
				});
			}
			
		},
		add:function(items) {
			var target_size = this.store.data.length + items.length;
			if (this.size == 0) {
				//unlimited
			} else if (this.size == 1) {
				this.store.setData([]);
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
			for (var i in items) {
				this.store.put(items[i]);
			}
			if (items.length > 0) {
				self.gridNode.style.display = 'block';
			} else {
				self.gridNode.style.display = 'none';
			}
			self.refresh();
			dojo.global.dispatchEvent(new Event('resize'));
		},
		edit: function(item_id) {
			this.dialog.show(item_id);
		},
		remove: function(item_id) {
			this.store.remove(item_id);
			this.refresh();
		},
		refresh: function() {
			var ids = [];
			var items = this.store.data;
			for (var i in items) ids.push('#' + this.store.getIdentity(items[i]));
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
		newItem: function() {
			this.dialog.show();
		}
	});
});
