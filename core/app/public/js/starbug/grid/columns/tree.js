define(["dojo", "dojo/on", "sb", "put-selector/put"],
function(dojo, on, sb, put, tree){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.tree = function(column){

		column.className = "w3";

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options){
			put(parent && parent.contents ? parent : cell, ".dgrid-tree");
		};

		column.renderHeaderCell = function(node) {
			put(node, '.dgrid-tree');
			put(node, 'div.material-icons', "add_circle");
		}

		column.renderExpando = true;

		return column;
	};
});
