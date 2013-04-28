define(["dojo", "dojo/when", "sb", "put-selector/put", "dgrid/editor", "dijit/form/Select"],
function(dojo, when, sb, put, editor, Select){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.select = function(column){
		
		column.options = column.options || {};
		
		//allow specifying a range, eg. '1-5'
		if (column.range) {
			range = column.range.split('-');
			range[0] = parseInt(range[0]);
			range[1] = parseInt(range[1]);
			for (var i = range[0];i<=range[1];i++) column.options[i] = i;
		}

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options, header){
			put(parent && parent.contents ? parent : cell, ".dgrid-select");
			if (value) {
				if (column.options && column.options[value]) populate(cell, column.options[value]);
				else if (value == "NULL") populate(cell, "");
				else if (column.from) {
					when(column.editorInstance._queryRes, function(data) {
						items = column.editorInstance.getOptions();
						for (var i in items) if (value == items[i].value) value = items[i].label;						
						populate(cell, value);
					});
				}
			}
		};
		
		var populate = function(cell, value) {
			put(cell, 'span.'+value.replace(/[^a-zA-Z0-9 ]/g, '').replace(/ /g, '-').replace(/[\/\\]/g, '').toLowerCase(), value);
		};

		column.editorArgs = {style:'width:100%'};
		
		if (column.from) {
			column.init = function() {
				column.grid.on('.dgrid-cell:dgrid-editor-show', function(evt) {
					column.editorInstance.set('value', evt.cell.row.data[column.field]);
				});
			};
			column.editorArgs.store = sb.get(column.from, 'select');
			if (column.query) column.editorArgs.query = query;
			column.editorArgs.labelAttr = 'label';
			column.editorArgs.onSetStore = function(store, items) {
				this.options.unshift({label:'&nbsp;', value:'NULL'});
				this._loadChildren();
			};
			column.editorArgs.sortByLabel = false;
			column.editorArgs.onFetch = function(data) {
				console.log(data);
			};
		} if (column.options) {
			column.editorArgs.options = [];
			for (var o in column.options) column.editorArgs.options.push({label:column.options[o].toString(), value:o.toString()});
		}

		column = editor(column, Select, "dblclick");
				
		return column;
	};
});
