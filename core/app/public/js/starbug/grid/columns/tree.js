define(["dojo", "dojo/on", "sb", "put-selector/put", "dgrid/tree"],
function(dojo, on, sb, put, tree){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.tree = function(column){

		column = tree(column);
				
		return column;
	};
});
