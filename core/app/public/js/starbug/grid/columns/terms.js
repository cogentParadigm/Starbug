define(["dojo", "dojo/when", "sb/store/Api", "put-selector/put", "starbug/form/MultiSelect"],
function(dojo, when, Api, put, MultiSelect){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.terms = function(column){

		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-terms");
			//get groups and populate field
			var node = put(cell, 'div.terms.'+column.taxonomy);
			values = [];
			if (typeof value == "string") values = value.split(/,/g);
			//items = column.grid._editorInstances[column.id].getOptions();
			var list = [];
			sb.get('terms', 'select').filter({taxonomy:column.taxonomy, id:value}).forEach(function(item) {
				if (values.indexOf(item.id) != -1 || values.indexOf(item.label) != -1) list.push('<span class="term '+item.label+'">'+item.label+'</span>');
			}).then(function() {
				node.innerHTML = list.join(', ');
			});
		};

		//column.editorArgs = {style:'width:100%', mode:'csv', store:sb.get('terms', 'select'), query:{taxonomy:column.taxonomy}};

		//column.editorArgs = {style:'width:100%', mode:'csv', store:new Api({model:'terms', action:'select'}), query:{taxonomy:column.taxonomy}};
		//column.editor = MultiSelect;
		//column.editOn = column.editOn || "dblclick";

		return column;
	};
});
