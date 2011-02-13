dojo.provide("starbug.grid.EnhancedGrid");
dojo.require("starbug.data.ItemFileWriteStore");
dojo.require("dojox.grid.cells.dijit");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.NestedSorting");
dojo.require("dojox.grid.enhanced.plugins.DnD");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.grid.enhanced.plugins.IndirectSelection");
//dojo.require("dojox.grid.enhanced.plugins.CellFormatter"); //not available til dojo 1.6
dojo.declare('starbug.grid.EnhancedGrid', dojox.grid.EnhancedGrid, {
	model: '',
	action: 'create',
	storeUrl: '',
	store: null,
	mouse_down: false,
	refreshInterval: 0,
	scrollPosition: 0,
	timer: null,
	orderColumn: '',
	onReload: null,
	postCreate: function() {
		this.inherited(arguments);
		this.store = new starbug.data.ItemFileWriteStore({url: this.storeUrl});
		this.store.model = this.model;
		this.store.action = this.action;
		dojo.connect(this, "onMouseDown", this, 'setMouseDown');
		dojo.connect(this, "onMouseUp", this, 'setMouseUp');
		if (this.orderColumn != '') dojo.subscribe(this.rowMovedTopic, this, 'dropSelectedRows');
		if (this.refreshInterval > 0) {
			this.timer = setInterval(dojo.hitch(this, 'reloadStore'), this.refreshInterval*1000);
			dojo.connect(window, "onscroll", this, 'resetInterval');
		}
		if (this.onReload != null) this.onReload();
		dojo.behavior.apply();
	},
	resetInterval: function() {
		clearInterval(this.timer);
		this.timer = setInterval(dojo.hitch(this, 'reloadStore'), this.refreshInterval*1000);
	},
	reloadStore: function() {
		if (this.store) {
			if (this.mouse_down || this.edit.isEditing()) return;
			else if (this.store.isDirty()) {
				this.store.save({onComplete: dojo.hitch(this, 'reloadStore')});
				return;
			}
		}
		this.scrollPosition = dojo._docScroll().y;
		dojo.xhrGet({
			url: this.storeUrl,
			handleAs: 'json',
			load: dojo.hitch(this, function(data) {
				var q = this.query;
				this.setStore(new starbug.data.ItemFileWriteStore({
					data: {
						identifier: 'id',
						items: data.items
					}
				}));
				this.setQuery(q);
				this.store.model = this.model;
				this.store.action = this.action;
				this.store.url = this.storeUrl;
				this.update();
				window.scrollTo(0, this.scrollPosition);
				if (this.onReload != null) this.onReload();
				dojo.behavior.apply();
			})
		});
	},
	setMouseDown: function() {
		this.mouse_down = true;
	},
	setMouseUp: function() {
		this.mouse_down = false;
	},
	dropSelectedRows: function() {
		var rows = this.selection.getSelected();
		var new_index = this.selection.selectedIndex; //current physical index in the grid
		this.moveRows(rows, new_index);
	},
	moveRowToTop: function(rowIndex) {
		this.moveRows([this.getItem(rowIndex)], 0);
	},
	moveRowToBottom: function(rowIndex) {
		this.moveRows([this.getItem(rowIndex)], this._by_idx.length-1);
	},
	moveRows: function(rows, toIndex) {
		var new_order = this.store._arrayOfAllItems[toIndex][this.orderColumn][0];
		for (var i in rows) {
			i = rows[i];
			this.store.setValue(this.store._arrayOfAllItems[i._0], this.orderColumn, new_order);
			new_order++;
		}
		this.reloadStore();
	}
});
starbug.grid.EnhancedGrid.markupFactory = dojox.grid.EnhancedGrid.markupFactory;
starbug.grid.EnhancedGrid.registerPlugin = dojox.grid.EnhancedGrid.registerPlugin;