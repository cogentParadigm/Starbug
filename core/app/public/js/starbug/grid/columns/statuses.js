define(["dojo", "sb", "put-selector/put", "dgrid/editor", "dijit/form/Select"],
function(dojo, sb, put, editor, Select){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.statuses = function(column){
		
		var grid;


		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-statuses");
			value = parseInt(value);
			//get statuses and populate field
			items = column.editorInstance.getOptions();
			var list = [];
			var node = put(cell, 'span.statuses') 
			for (var s in items) if (value & parseInt(items[s].value)) list.push('<span class="'+items[s].label+'">'+items[s].label+'</span>');
			node.innerHTML = list.join(', ');
		};
	
	 column.init = function() {
		 grid = column.grid;
		 column.editorArgs.store = sb.get(column.grid.model, 'statuses');
		 
	 };

		column.editorArgs = {style:'width:100%', labelAttr:'label', multiple:true};

		column = editor(column, Select, "dblclick");
				
		return column;
	};
});
