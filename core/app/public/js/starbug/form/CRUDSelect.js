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
	"dstore/Memory",
	"dojo/ready",
	"starbug/form/Dialog",
	"starbug/grid/MemoryGrid",
	"starbug/grid/columns/handle",
	"starbug/grid/columns/html",
	"starbug/grid/columns/options"
], function (dojo, declare, lang, Widget, Templated, _WidgetsInTemplate, sb, template, put, on, query, domclass, Deferred, Memory, ready, Dialog) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		value:[],
		model:'',
		templateString: template, //the template (./templates/FileSelect.html)
		widgetsInTemplate: true,
		input_name:'file',
		size:0,
		store:null,
		dialog:null,
		get_data:false,
		post_data:false,
		editing:false,
		join:true,
		postCreate:function() {
			var self = this;
			this.get_data = this.get_data || {};
			this.post_data = this.post_data || {};
			this.store = new Memory({data: []});
			this.dialog = new Dialog({url:"admin/"+self.model+"/", get_data:self.get_data, post_data:self.post_data, callback:function(data) {
				var object_id = query('input[name="'+self.model+'[id]"]').attr('value')[0];
				if (false !== self.editing) {
					if (object_id != self.editing) {
						self.store.remove(self.editing);
					}
					self.editing = false;
				}
				self.dialog.set('content', '');
				sb.get(self.model, 'select').filter({'id':object_id}).fetch().then(function(data) {
					self.add(data);
				});
			}});
			this.dialog.startup();
			self.grid.editor = self;

			// initialize hidden input
			this.input.name = this.input_name;
		},
		startup: function() {
			var self = this;
			self.grid.set('collection', self.store);

			if (self.value.length > 0) {
				sb.get(self.model, 'select').filter({'id':self.value.join(',')}).fetch().then(function(data) {
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
			for (var i = 0;i<items.length;i++) {
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
			this.editing = item_id;
			this.dialog.show(item_id);
		},
		remove: function(item_id) {
			this.store.remove(item_id);
			this.refresh();
		},
		copy:function(item_id) {
			this.dialog.show(false, {copy:item_id});
		},
		refresh: function() {
			this.grid.refresh();
			this.grid.renderArray(this.store.data);
			this.grid.set('collection', this.store);
			var ids = [];
			var items = this.store.data;
			for (var i = 0;i<items.length;i++) {
				var prefix = this.join ? '#' : '';
				ids.push(prefix + this.store.getIdentity(items[i]));
			}
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
