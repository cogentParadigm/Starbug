define(["dojo/_base/lang", "dojo/on", "put-selector/put"],
function(lang, on, put){
	window.app = window.app || {};
	window.app.shop = window.app.shop || {};
	window.app.shop.columns = window.app.shop.columns || {};
	window.app.shop.columns.quantity = function(column){


		column.renderCell = function(object, value, cell, options, header){
			if (!column.loaded) {
				column.loaded = true;
				console.log(column.editorInstance);
			}
		};

		column.editor = 'text';
		//column.editOn = column.editOn || "dblclick";

		return column;
	};
});
