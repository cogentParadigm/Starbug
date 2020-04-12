define(["dojo", "sb/strings", "put-selector/put", "dojo/on"],
function(dojo, strings, put, on){
	dojo.global.starbug.form = dojo.global.starbug.form || {};
	dojo.global.starbug.form.columns = dojo.global.starbug.form.columns || {};
	dojo.global.starbug.form.columns.FileSelectOptions = function(column){

		column.sortable = false;

		column.renderCell = function(object, value, cell, options, header){
			var grid = this.grid, parent = cell.parentNode;
			put(parent && parent.contents ? parent : cell, ".field-options");

			var div = put(cell, 'div.btn-group');

			//delete button
			var remove = 'javascript:;';
			remove = put(div, 'a.btn.btn-default[title=Remove][href='+remove+']', put('div.fa.fa-times'));
			on(remove, 'click', function() {
				if (confirm('Are you sure you want to remove this item?')) {
					grid.editor.remove(object.id);
				}
			});
		};

		column.renderHeaderCell = function(node) {
			put(node, '.field-options', 'Options');
		}

		return column;
	};
});
