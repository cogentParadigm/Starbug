define(["dojo", "sb", "put-selector/put", "dgrid/editor", "dijit/form/Select"],
function(dojo, sb, put, editor, Select){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.groups = function(column){

		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-groups");
			value = parseInt(value);
			//get groups and populate field
			//items = column.editorInstance.getOptions();
			var node = put(cell, 'span.groups');
			sb.query('groups').then(function(items) {
				var list = [];
				for (var i in items) if (value & parseInt(items[i].id)) list.push('<span class="'+items[i].label+'">'+items[i].label+'</span>');
				node.innerHTML = list.join(', ');
			});
		};

		//column.editorArgs = {style:'width:100%', labelAttr:'label', store:sb.get('groups')};
		//column = editor(column, Select, "dblclick");
		column = editor(column, "text", "dblclick");
				
		return column;
	};
});
