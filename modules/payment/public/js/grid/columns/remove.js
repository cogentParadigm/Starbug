define(["dojo", "sb/strings", "put-selector/put", "dojo/on"],
function(dojo, strings, put, on){
	window.app = window.app || {};
	window.app.shop = window.app.shop || {};
	window.app.shop.columns = window.app.shop.columns || {};
	window.app.shop.columns.remove = function(column){

		column.sortable = false;

		column.renderCell = function(object, value, cell, options, header){
			var grid = this.grid, parent = cell.parentNode;
			put(parent && parent.contents ? parent : cell, ".dgrid-options");

			var div = put(cell, 'div.btn-group');

			//delete button
			var remove = 'javascript:;';
			remove = put(div, 'a.Delete.btn.btn-danger[title=Delete][href='+remove+']', 'Remove');
			on(remove, 'click', function() {
				if (confirm('Are you sure you want to delete this item?')) {
					if (typeof grid['editor'] != "undefined") {
						grid.editor.remove(object.id, object.type);
					} else {
						var d = grid.collection.remove(object.id);
						d.then(function() {grid.refresh();});
					}
				}
			});
		};

		return column;
	};
});
