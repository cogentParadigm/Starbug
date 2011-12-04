define([
	"dojo",
	"dojox",
	"dojox/grid/cells",
	"starbug/store/ObjectStore"
], function (dojo, dojox, cells, ObjectStore) {
var select = dojo.declare("starbug.grid.cells.Select", dojox.grid.cells.Select, {
	options: [],
	values: [],
	query: '',
	model: '',
	models: '',
	caption: '',
	value_field:'id',
	store: null,
	labelClass:false,
	postCreate: function() {
		this.models = this.query.split('  ', 1)[0];
		this.model = this.models.split('.', 1)[0];
		this.store = new ObjectStore({apiQuery: this.query, onItem:dojo.hitch(this,'onItem')});
	},
	onItem: function(item, store) {
		var label = this.caption;
		for (var i in item) {
			if (-1 !== this.caption.indexOf('%'+i+'%')) label = label.replace('%'+i+'%', item[i][0]);
		}
		this.options.push(label);
		this.values.push(item[this.value_field][0]);
	},
	formatter: function(data, rowIndex) {
		var label = this.caption;
		var item = this.store._getItemByIdentity(data);
		for (var i in item) {
			if (-1 !== this.caption.indexOf('%'+i+'%')) label = label.replace('%'+i+'%', this.store.getValue(item, i));
		}
		var itemClass = this.model;
		if (this.labelClass) itemClass += ' '+label;
		return '<span class="'+itemClass+'">'+label+'</span>';
	
	}
});
starbug.grid.cells.Select.markupFactory = function(node, cell){
	dojox.grid.cells.Cell.markupFactory(node, cell);
};
dojo.declare("starbug.grid.cells.Status", select, {
	query: 'statuses',
	caption: '%name%',
	labelClass: true
});
starbug.grid.cells.Status.markupFactory = function(node, cell){
	dojox.grid.cells.Cell.markupFactory(node, cell);
};
dojo.declare("starbug.grid.cells.Group", dojox.grid.cells.Select, {
	query: 'groups',
	caption: '%name%',
	labelClass: true
});
starbug.grid.cells.Group.markupFactory = function(node, cell){
	dojox.grid.cells.Cell.markupFactory(node, cell);
};
dojo.declare("starbug.grid.cells.TextArea", dojox.grid.cells.Cell, {
	formatEditing: function(inDatum, inRowIndex){
		this.needFormatNode(inDatum, inRowIndex);
		return '<textarea class="dojoxGridInput">' + inDatum + '</textarea>';
	},
	getValue: function(inRowIndex){
		var n = this.getEditNode(inRowIndex);
		console.log(this.view.getCellNode(inRowIndex, this.index));
		console.log(inRowIndex);
		console.log(this.index);
		return n.innerHTML;
	}
});
return select;
});
