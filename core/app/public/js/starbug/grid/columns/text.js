define(["dojo", "dojo/on", "sb", "put-selector/put", "dgrid/editor", "sb/markdown"],
function(dojo, on, sb, put, editor, marked){
	marked.setOptions({breaks:true, smartLists:true});
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.text = function(column){
		
		column.dismissOnEnter = false;
		
		column.ctrlDown = false;
		column.loaded = false;

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options, header){
			if (!column.loaded) {
				column.loaded = true;
				on(column.editorInstance, "keydown", function(evt) {
					var key = evt.keyCode || evt.which;
					if (key == 17) column.ctrlDown = true;
				});
				on(column.editorInstance, "keyup", function(evt) {
					var key = evt.keyCode || evt.which;
					if (key == 17) column.ctrlDown = false;
					else if (column.ctrlDown && key == 13) {
						column.editorInstance.blur();
						column.ctrlDown = false;
					}
				});
			}

			put(parent && parent.contents ? parent : cell, ".dgrid-textarea");

			//node = put(cell, 'pre');
			cell.innerHTML = marked(value);//sb.strings.htmlentities(value);
		};

		column = editor(column, 'textarea', "dblclick");
				
		return column;
	};
});
