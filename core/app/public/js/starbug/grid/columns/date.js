define(["dojo", "sb", "put-selector/put", "dgrid/editor", "dijit/form/DateTextBox"],
function(dojo, sb, put, editor, DateTextBox){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.date = function(column){
		
		column.options = column.options || {};

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-date");
			node = put(cell, 'span');
			node.innerHTML = sb.strings.date(value);
		};

		column = editor(column, DateTextBox, "dblclick");
				
		return column;
	};
});
