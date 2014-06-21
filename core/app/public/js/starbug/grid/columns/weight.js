define(["dojo", "sb/strings", "put-selector/put", "dojo/on", "dgrid/editor", "dijit/form/NumberSpinner"],
function(dojo, strings, put, on, editor, NumberSpinner){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.weight = function(column){
		
		var grid;
		column.init = function(){
			grid = column.grid;
		};

		column.renderCell = function(object, value, cell, options, header){
			var url, text = '', row = object && grid.row(object), parent = cell.parentNode;
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
				grid.store.put(record).then(function() {
					grid.refresh();
				});
				evt.preventDefault();
				return false;
			});
			
		};

		column = editor(column, NumberSpinner, "dblclick");
		
		return column;
	};
});
