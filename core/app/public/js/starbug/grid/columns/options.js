define(["dojo", "sb/strings", "put-selector/put", "dojo/on"],
function(dojo, strings, put, on){
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
			if (typeof grid['dialog'] == 'undefined') var edit = dojo.global.location.href+'/update/'+row.id;
			else var edit = 'javascript:'+grid['dialog']+'.show('+row.id+')';
			var remove = 'javascript:;';
			put(cell, 'a.Edit.button[title=Edit][href='+edit+']', put('div.sprite.icon'));
			remove = put(cell, 'a.Delete.button[title=Delete][href='+remove+']', put('div.sprite.icon'));
			on(remove, 'click', function() {
				if (confirm('Are you sure you want to delete this item?')) {
					var d = grid.store.remove(row.id);
					d.then(function() {grid.refresh();});
				}
			});
		};

		return column;
	};
});
