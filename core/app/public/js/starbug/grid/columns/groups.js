define(["dojo", "sb", "put-selector/put", "starbug/form/MultiSelect"],
function(dojo, sb, put, MultiSelect){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.groups = function(column){

		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-groups");
			value = parseInt(value);
			//get groups and populate field
			items = column.grid._editorInstances[column.id].getOptions();
			var node = put(cell, 'span.groups');
			var list = [];
			for (var i in items) if (value & parseInt(items[i].value)) list.push('<span class="'+items[i].label+'">'+items[i].label+'</span>');
			node.innerHTML = list.join(', ');
		};

		column.editorArgs = {style:'width:100%', store:sb.get('groups')};
		column.editor = MultiSelect;
		column.editOn = column.editOn || "dblclick";

		return column;
	};
});
