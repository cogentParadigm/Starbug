define([
	"dojo",
	"dojo/behavior",
	"dojox/grid/EnhancedGrid",
	"starbug/data/ObjectStore",
	"dojox/grid/cells/dijit",
	"dojox/grid/enhanced/plugins/NestedSorting",
	"dojox/grid/enhanced/plugins/DnD",
	"dojox/grid/enhanced/plugins/Menu",
	"dojox/grid/enhanced/plugins/IndirectSelection"
], function(dojo, behavior, dojoxGrid, ObjectStore){

var EnhancedGrid = dojo.declare('starbug.grid.EnhancedGrid', dojoxGrid, {
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
		args.apiQuery = args.models+'  query:'+args.apiQuery;
		this.store = new ObjectStore(args);
		this.model = this.store.model;
		this.models = this.store.models;
	},
	postCreate: function() {
		this.inherited(arguments);
		dojo.connect(this, "onMouseDown", this, 'setMouseDown');
		dojo.connect(this, "onMouseUp", this, 'setMouseUp');
		if (this.orderColumn != '') dojo.subscribe(this.rowMovedTopic, this, 'dropSelectedRows');
		setTimeout(dojo.hitch(behavior, 'apply'), 1000);
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
			this.store.setValue(i, this.orderColumn, new_order);
			new_order++;
		}
		this.store.save();
	},
	doApplyCellEdit: function(inValue, inRowIndex, inAttrName){
		this.store.setValue(this.getItem(inRowIndex), inAttrName, inValue);
		this.onApplyCellEdit(inValue, inRowIndex, inAttrName);
	},
	_resize: function(changeSize, resultSize) {
		var scrollPosition = dojo._docScroll().y;
		this.inherited(arguments);
		window.scrollTo(0, scrollPosition);
		dojo.behavior.apply();
	}
});
EnhancedGrid.markupFactory = dojoxGrid.markupFactory;
EnhancedGrid.registerPlugin = dojoxGrid.registerPlugin;
return EnhancedGrid;
});

