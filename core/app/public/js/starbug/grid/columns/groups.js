define(["dojo", "sb", "put-selector/put"],
function(dojo, sb, put){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.groups = function(column){

		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-groups");
			value = parseInt(value);
			//get groups and populate field
			sb.query('groups').then(function(items) {
				for (var g in items) if (value & items[g].id) put(cell, 'span.'+items[g].name, items[g].name);
			});
		};

		return column;
	};
});
