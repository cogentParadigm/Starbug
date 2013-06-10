define(["dojo", "dojo/when", "sb", "put-selector/put", "dgrid/editor", "dijit/form/Select"],
function(dojo, when, sb, put, editor, Select){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.statuses = function(column){
		
		var grid;


		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-statuses");
			value = parseInt(value);
			//get statuses and populate field
			
			when(column.editorInstance._queryRes, function(data) {
				items = column.editorInstance.getOptions();
				var list = [];
				var node = put(cell, 'span.statuses') 
				for (var s in items) if (value & parseInt(items[s].value)) list.push('<span class="status '+items[s].label+'">'+items[s].label.replace('_', ' ')+'</span>');
				node.innerHTML = list.join(', ');
			});
		};
	
	 column.init = function() {
		 grid = column.grid;
		 column.editorArgs.store = sb.get(column.grid.model, 'statuses');
		 
	 };

		column.editorArgs = {style:'width:100%', labelAttr:'label', multiple:true};
 		column.editorArgs.onSetStore = function(store, items) {
			for (var i in this.options) this.options[i].label = this.options[i].label.replace('_', ' ').replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
 			this._loadChildren();
 		};

		column = editor(column, Select, "dblclick");
				
		return column;
	};
});
