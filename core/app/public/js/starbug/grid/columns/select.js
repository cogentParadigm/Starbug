define(["dojo", "sb", "put-selector/put", "dgrid/editor", "dijit/form/Select"],
function(dojo, sb, put, editor, Select){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.select = function(column){
		
		column.options = column.options || {};
		
		//allow specifying a range, eg. '1-5'
		if (column.range) {
			range = column.range.split('-');
			range[0] = parseInt(range[0]);
			range[1] = parseInt(range[1]);
			for (var i = range[0];i<=range[1];i++) column.options[i] = i;
		}

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-select");;
			if (column.options && column.options[value]) value = column.options[value];
			else {
				items = column.editorInstance.getOptions();
				for (var i in items) if (value == items[i].value) value = '<span class="'+items[i].label.replace(/ /g, '-').toLowerCase()+'">'+items[i].label+'</span>';
			}
			node = put(cell, 'span');
			node.innerHTML = value;
		};

		column.editorArgs = {style:'width:100%'};
		
		if (column.from) {
			column.editorArgs.store = sb.get(column.from, 'select');
			if (column.query) column.editorArgs.query = query;
			column.editorArgs.labelAttr = 'label';
		} if (column.options) {
			column.editorArgs.options = [];
			for (var o in column.options) column.editorArgs.options.push({label:column.options[o].toString(), value:o.toString()});
		}

		column = editor(column, Select, "dblclick");
				
		return column;
	};
});
