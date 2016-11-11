define([
	"dojo/_base/declare",
	"dijit/_WidgetBase",
	"dijit/_TemplatedMixin",
	"dijit/_WidgetsInTemplateMixin",
	"sb",
	"dojo/text!./templates/Selection.html",
	"put-selector/put",
	"dojo/on",
	"dojo/query",
	"dojo/dom-class",
	"dojo/_base/Deferred",
	"dstore/Memory",
	"starbug/form/Dialog",
	"starbug/grid/MemoryGrid",
	"starbug/grid/columns/handle",
	"starbug/grid/columns/html",
	"starbug/grid/columns/options"
], function (declare, Widget, Templated, _WidgetsInTemplate, sb, template, put, on, query, domclass, Deferred, Memory, Dialog) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		value:[],
		model:'',
		templateString: template, //the template (./templates/FileSelect.html)
		widgetsInTemplate: true,
		input_name:'file',
		size:0,
		collection:null,
		prefix:'',
		postCreate:function() {
			this.collection = new Memory({data: []});
			this.grid.editor = self;
			this.input.name = this.input_name;
		},
		startup: function() {
			var self = this;
			self.grid.set('collection', this.collection);
			if (self.value.length > 0) {
				sb.get(self.model, 'select').filter({'id':self.value.join(',')}).fetch().then(function(data) {
					self.add(data);
				});
			}
		},
		add:function(items) {
			var target_size = this.collection.data.length + items.length;
			if (this.size == 0) {
				//unlimited
			} else if (this.size == 1) {
				this.collection.setData([]);
			} else if (target_size == this.size) {
				this.controls.style.display = 'none';
			} else if (target_size > this.size) {
				alert("You have reached the limit.");
				return;
			} else {
				this.controls.style.display = 'block';
			}
			this.set_status();
			for (var i = 0;i<items.length;i++) {
				this.collection.put(items[i]);
			}
			if (items.length > 0) {
				this.gridNode.style.display = 'block';
			} else {
				this.gridNode.style.display = 'none';
			}
			this.refresh();
			window.dispatchEvent(new Event('resize'));
		},
		remove: function(item_id) {
			this.collection.remove(item_id);
			this.refresh();
		},
		refresh: function() {
			this.grid.refresh();
			this.grid.renderArray(this.collection.data);
			this.grid.set('collection', this.collection);
			var ids = [];
			var items = this.collection.data;
			for (var i = 0;i<items.length;i++) {
				ids.push(this.prefix + this.collection.getIdentity(items[i]));
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
		}
	});
});
