define(["dojo", "sb/strings", "put-selector/put", "dojo/on"],
function(dojo, strings, put, on){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.taxonomy_options = function(column){

		column.sortable = false;

		column.renderCell = function(object, value, cell, options, header){
			var grid = this.grid, row = object && grid.row(object), parent = cell.parentNode;
			var base_url = grid.base_url || dojo.global.location.pathname;
			put(parent && parent.contents ? parent : cell, ".dgrid-options");

			var div = put(cell, 'div.btn-group');

			//edit button
			var href = base_url+'/taxonomy/'+row.id+dojo.global.location.search;
			var edit = put(div, 'a.Edit.btn.btn-default[title=Edit][href='+href+']', put('div.fa.fa-edit'));

			//delete button
			var remove = 'javascript:;';
			remove = put(div, 'a.Delete.btn.btn-default[title=Delete][href='+remove+']', put('div.fa.fa-times'));
			on(remove, 'click', function() {
				if (confirm('Are you sure you want to delete this item?')) {
					var d = grid.collection.remove({taxonomy:row.id});
					d.then(function() {grid.refresh();});
				}
			});
		};

		return column;
	};
});
