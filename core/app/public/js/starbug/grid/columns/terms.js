define(["dojo", "dojo/when", "sb", "put-selector/put", "starbug/form/MultiSelect"],
function(dojo, when, sb, put, MultiSelect){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.terms = function(column){
		console.log(column);

		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-terms");
			//get groups and populate field
			var node = put(cell, 'div.terms.'+column.taxonomy);
			items = column.grid._editorInstances[column.id].getOptions();
			values = [];
			if (typeof value == "string") values = value.split(/,/g);
			var list = [];
			for (var i in items) {
				if (values.indexOf(items[i].value) != -1 || values.indexOf(items[i].label) != -1) list.push('<span class="term '+items[i].label+'">'+items[i].label+'</span>');
			}
			node.innerHTML = list.join(', ');
		};

		column.editorArgs = {style:'width:100%', mode:'csv', store:sb.get('terms', 'select'), query:{taxonomy:column.taxonomy}};
		column.editor = MultiSelect;
		column.editOn = column.editOn || "dblclick";

		return column;
	};
});
