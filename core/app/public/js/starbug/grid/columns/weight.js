define(["dojo", "sb/strings", "put-selector/put", "dojo/on", "dijit/form/NumberSpinner"],
function(dojo, strings, put, on, editor, NumberSpinner){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.weight = function(column){

		column.renderCell = function(object, value, cell, options, header){
			var grid = this.grid, row = object && grid.row(object), parent = cell.parentNode;
			var base_url = grid.base_url || dojo.global.location.pathname;
			put(parent && parent.contents ? parent : cell, ".dgrid-weight");

			put(cell, 'div.pull-left[style="margin-right:15px"]', value);

			var div = put(cell, 'div.btn-group');

			//clear button
			if (typeof grid['dialog'] != 'undefined') var href = 'javascript:'+grid['dialog']+'.show('+row.id+')';
			else var href = base_url+'/update/'+row.id+dojo.global.location.search;
			var clear = put(div, 'a.btn.btn-default[title=Edit][href="javascript:;"]', put('div.fa.fa-recycle'));
			on(clear, 'click', function(evt) {
				var record = {id:row.id};
				record[column.field] = 0;
				grid.collection.put(record).then(function() {
					grid.refresh();
				});
				evt.preventDefault();
				return false;
			});

		};

		column.editor = NumberSpinner;
		column.editOn = column.editOn || "dblclick";

		return column;
	};
});
