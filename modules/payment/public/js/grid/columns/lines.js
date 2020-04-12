define(["dojo", "sb/strings", "put-selector/put", "dojo/on"],
function(dojo, strings, put, on){
	window.payment = window.payment || {};
	window.payment.grid = window.payment.grid || {};
	window.payment.grid.columns = window.payment.grid.columns || {};
	window.payment.grid.columns.lines = function(column){

		column.sortable = false;

		column.renderCell = function(object, value, cell, options, header){
			var grid = this.grid, row = object && grid.row(object), parent = cell.parentNode;
			var base_url = grid.base_url || dojo.global.location.pathname;
			//put(parent && parent.contents ? parent : cell, ".dgrid-options");
			var div = put(cell, 'div.btn-group');
			//details
			put(div, 'a.Details.btn.btn-default[title=Details][href='+base_url+'/'+object.type+'/'+row.id+']', put('div.fa.fa-desktop'));
		};

		return column;
	};
});
