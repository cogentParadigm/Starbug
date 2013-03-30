define(["dojo", "sb", "put-selector/put", "dgrid/editor", "starbug/form/MultiSelect"],
function(dojo, sb, put, editor, MultiSelect){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.groups = function(column){

		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-groups");
			value = parseInt(value);
			//get groups and populate field
			items = column.editorInstance.getOptions();
			var node = put(cell, 'span.groups');
			var list = [];
			for (var i in items) if (value & parseInt(items[i].value)) list.push('<span class="'+items[i].label+'">'+items[i].label+'</span>');
			node.innerHTML = list.join(', ');
		};

		column.editorArgs = {style:'width:100%', store:sb.get('groups')};
		column = editor(column, MultiSelect, "dblclick");
				
		return column;
	};
});
