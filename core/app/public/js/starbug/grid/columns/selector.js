define(["dojo", "dojo/aspect", "sb", "put-selector/put", "dgrid/selector"],
function(dojo, aspect, sb, put, selector) {
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.selector = function(column){

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options){
			put(parent && parent.contents ? parent : cell, ".dgrid-selector");
		};

		column.selectorType = "checkbox";

		column = selector(column);

		aspect.after(column, "renderHeaderCell", function(node) {
			put(node, '.dgrid-selector');
		}, true);

		return column;
	};
});
