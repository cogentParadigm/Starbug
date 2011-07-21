dojo.provide("starbug.grid.EnhancedGrid");
dojo.require("starbug.grid.cells");
dojo.require("starbug.data.ApiStore");
dojo.require("dojox.grid.cells.dijit");
dojo.require("dojox.grid.EnhancedGrid");
dojo.require("dojox.grid.enhanced.plugins.NestedSorting");
dojo.require("dojox.grid.enhanced.plugins.DnD");
dojo.require("dojox.grid.enhanced.plugins.Menu");
dojo.require("dojox.grid.enhanced.plugins.IndirectSelection");
dojo.require("dojox.dtl.filter.strings");
dojo.declare('starbug.grid.EnhancedGrid', dojox.grid.EnhancedGrid, {
	model: '',
	models: '',
	action: 'create',
	apiQuery:'',
	storeUrl: '',
	store: null,
	mouse_down: false,
	orderColumn: '',
	startTime: '',
	notifier:'',
	hasFetched: false,
	constructor: function(args) {
		args.query = args.models+'  query:'+args.apiQuery;
		args.onComplete = dojo.hitch(this, '_onFetchComplete');
		this.store = new starbug.data.ApiStore(args);
		this.model = this.store.model;
		this.models = this.store.models;
	},
	postCreate: function() {
		this.inherited(arguments);
		dojo.connect(this, "onMouseDown", this, 'setMouseDown');
		dojo.connect(this, "onMouseUp", this, 'setMouseUp');
		if (this.orderColumn != '') dojo.subscribe(this.rowMovedTopic, this, 'dropSelectedRows');
		setTimeout(dojo.hitch(dojo.behavior, 'apply'), 1000);
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
		this.moveRows(rows, new_index+1);
	},
	moveRowToTop: function(rowIndex) {
		this.moveRows([this.getItem(rowIndex)], 0);
	},
	moveRowToBottom: function(rowIndex) {
		this.moveRows([this.getItem(rowIndex)], this._by_idx.length-1);
	},
	moveRows: function(rows, toIndex) {
		var new_order = this.store.getValue(grid.getItem(toIndex), this.orderColumn);
		for (var i in rows) {
			i = rows[i];
			this.store.setValue(this.store._arrayOfAllItems[i._0], this.orderColumn, new_order);
			new_order++;
		}
		this.store.save();
	},
	_onFetchComplete: function(items, req){
		this.inherited(arguments);
		if (!this.hasFetched) {
			this.hasFetched = true;
			this.setQuery();
		}
	},
	_resize: function(changeSize, resultSize) {
		var scrollPosition = dojo._docScroll().y;
		this.inherited(arguments);
		window.scrollTo(0, scrollPosition);
		dojo.behavior.apply();
	}
});
starbug.grid.EnhancedGrid.markupFactory = dojox.grid.EnhancedGrid.markupFactory;
starbug.grid.EnhancedGrid.registerPlugin = dojox.grid.EnhancedGrid.registerPlugin;
