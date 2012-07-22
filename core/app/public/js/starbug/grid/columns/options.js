define(["dojo", "sb/strings", "put-selector/put"],
function(dojo, strings, put){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.options = function(column){
		
		var grid;
		column.sortable = false;

		column.init = function(){
			grid = column.grid;
		};

		column.renderCell = function(object, value, cell, options, header){
			var url, text = '', row = object && grid.row(object), parent = cell.parentNode;
			put(parent && parent.contents ? parent : cell, ".dgrid-options");
			var edit = put(cell, 'a.Edit.button[title=Edit][href='+dojo.global.location.href+'/update/'+row.id+']', put('div.sprite.icon'));
			var remove = put(cell, 'a.Delete.button[title=Delete][href='+dojo.global.location.href+'/delete/'+row.id+']', put('div.sprite.icon'));
		};

		return column;
	};
});
