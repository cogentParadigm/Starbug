define(["dojo", "sb", "put-selector/put"],
function(dojo, sb, put){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.image = function(column){

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-image");
			node = put(cell, 'div');
			put(node, 'img[src="'+value+'"][style="height:100px"]');
		};
				
		return column;
	};
});
