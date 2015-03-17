define(["dojo", "sb", "put-selector/put"],
function(dojo, sb, put){
	dojo.global.starbug = dojo.global.starbug || {};
	dojo.global.starbug.grid = dojo.global.starbug.grid || {};
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.html = function(column){

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-html");
			node = put(cell, 'div');
			node.innerHTML = value;
		};
				
		return column;
	};
});
