define(["dojo", "sb", "put-selector/put"],
function(dojo, sb, put, editor){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.handle = function(column){

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options){
			put(parent && parent.contents ? parent : cell, ".dgrid-handle");
			node = put(cell, 'div.dojoDndHandle', put('div.icon-reorder'));
		};

		column.renderHeaderCell = function(node) {
			put(node, 'div.icon-reorder');
		}
				
		return column;

	};
});
