define(["dojo", "dojo/on", "sb", "put-selector/put", "dgrid/tree"],
function(dojo, on, sb, put, tree){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.tree = function(column){

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options){
			put(parent && parent.contents ? parent : cell, ".dgrid-tree");
		};

		column.renderHeaderCell = function(node) {
			put(node, '.dgrid-tree');
			put(node, 'div.fa.fa-plus-circle');
		}

		column = tree(column);
				
		return column;
	};
});
