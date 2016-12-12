define(["dojo", "sb/strings", "put-selector/put", "dojo/on"],
function(dojo, strings, put, on){
	dojo.global.payment = dojo.global.payment || {};
	dojo.global.payment.grid = dojo.global.payment.grid || {};
	dojo.global.payment.grid.columns = dojo.global.payment.grid.columns || {};
	dojo.global.payment.grid.columns.payment_gateway_options = function(column){

		var grid;
		column.sortable = false;
		column.init = function(){
			grid = column.grid;
		};

		column.renderCell = function(object, value, cell, options, header){
			var url, text = '', row = object && grid.row(object), parent = cell.parentNode;
			var base_url = grid.base_url || dojo.global.location.pathname;
			put(parent && parent.contents ? parent : cell, ".dgrid-options");

			var div = put(cell, 'div.btn-group');

			//edit button
			var href = "javascript:;";
			if (typeof grid['dialog'] == 'string') href = 'javascript:'+grid['dialog']+'.show('+row.id+')';
			else if (typeof grid['dialog'] == 'undefined') href = base_url+'/update/'+row.id+dojo.global.location.search;
			var edit = put(div, 'a.Edit.btn.btn-default[title=Edit][href='+href+']', put('div.fa.fa-edit'));
			if (typeof grid['dialog'] == "object") {
				on(edit, 'click', function(evt) {
					grid.dialog.show(row.id);
				});
			} else if (typeof grid['editor'] != 'undefined') {
				on(edit, 'click', function(evt) {
					grid.editor.edit(row.id);
					evt.preventDefault();
					return false;
				});
			}

			//copy button
			var chref = base_url + '/create?copy='+row.id;
			if (typeof grid.dialog == 'string') chref = 'javascript:'+grid.dialog+'.show(false, {copy:'+row.id+'})';
			var copy = put(div, 'a.Copy.btn.btn-default[title=Copy][href='+chref+']', put('div.fa.fa-files-o'));
			if (typeof grid.dialog == "object") {
				on(copy, 'click', function(e) {
					e.preventDefault();
					grid.dialog.show(false, {copy:row.id});
				});
			} else if (typeof grid.editor != "undefined") {
				on(copy, 'click', function(e) {
					e.preventDefault();
					grid.editor.copy(row.id);
				});
			}

			put(div, 'a.Settings.btn.btn-default[title=Settings][href='+base_url+'/settings/'+row.id+']', put('div.fa.fa-cogs'))

			//delete button
			var remove = 'javascript:;';
			remove = put(div, 'a.Delete.btn.btn-default[title=Delete][href='+remove+']', put('div.fa.fa-times'));
			on(remove, 'click', function() {
				if (confirm('Are you sure you want to delete this item?')) {
					if (typeof grid['editor'] != "undefined") {
						grid.editor.remove(row.id);
					} else {
						var d = grid.collection.remove(row.id);
						d.then(function() {grid.refresh();});
					}
				}
			});
		};

		return column;
	};
});
