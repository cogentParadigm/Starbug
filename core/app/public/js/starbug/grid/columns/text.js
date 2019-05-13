define(["dojo", "dojo/on", "sb", "put-selector/put", "sb/markdown"],
function(dojo, on, sb, put, marked){
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
				if (column.editor) {
					var editor = column.grid._editorInstances[column.id];
					on(editor, "keydown", function(evt) {
						var key = evt.keyCode || evt.which;
						if (key == 17) column.ctrlDown = true;
					});
					on(editor, "keyup", function(evt) {
						var key = evt.keyCode || evt.which;
						if (key == 17) column.ctrlDown = false;
						else if (column.ctrlDown && key == 13) {
							editor.blur();
							column.ctrlDown = false;
						}
					});
				}
			}

			put(parent && parent.contents ? parent : cell, ".dgrid-textarea");

			//node = put(cell, 'pre');
			cell.innerHTML = marked(value);//sb.strings.htmlentities(value);
		};

		return column;
	};
});
