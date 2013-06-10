define([
	"dojo",
	"starbug",
	"sb",
	"sb/data",
	"starbug/store/Api",
	"dgrid/GridFromHtml",
	"dgrid/OnDemandGrid",
	"dgrid/Keyboard",
	"dgrid/Selection",
	"dgrid/editor",
	"dojo/_base/Deferred",
	"dojo/dom-attr"
], function(dojo, starbug, sb, data, api, GridFromHtml, List, Keyboard, Selection, editor, Deferred, attr){
window.dgrid = window.dgrid || {GridFromHtml:GridFromHtml, editor:editor};
var Grid = dojo.declare('starbug.grid.Grid', [GridFromHtml, List, Keyboard, Selection], {
	keepScrollPosition:true,
	noDataMessage:'No Results',
	getBeforePut:false,
	constructor: function(args) {
		this.store = sb.get(args.model, args.action);
	},
	startup: function() {
		this.inherited(arguments);
		var grid = this;
		for (var i in this.columns) if (typeof this.columns[i]['editor'] != "undefined") this.columns[i].autoSave = true;
	},
	save: function () {
	
			// Keep track of the store and puts
			var self = this,
				store = this.store,
				dirty = this.dirty,
				dfd = new Deferred(), promise = dfd.promise,
				getFunc = function(id){
					// returns a function to pass as a step in the promise chain,
					// with the id variable closured
					var data;
					return (self.getBeforePut || !(data = self.row(id).data)) ?
						function(){ return store.get(id); } :
						function(){ return data; };
				};
			
			// function called within loop to generate a function for putting an item
			function putter(id, dirtyObj) {
				dirtyObj['id'] = id;
				// Return a function handler
				return function(object) {
					var colsWithSet = self._columnsWithSet,
						updating = self._updating,
						key, data;
					if(colsWithSet){
						// Apply any set methods in column definitions.
						// Note that while in the most common cases column.set is intended
						// to return transformed data for the key in question, it is also
						// possible to directly modify the object to be saved.
						for(key in colsWithSet){
							data = colsWithSet[key].set(dirtyObj);
							if(data !== undefined){ dirtyObj[key] = data; }
						}
					}
					
					updating[id] = true;
					// Put it in the store, returning the result/promise
					return Deferred.when(store.put(dirtyObj), function(result) {
						if (result.errors) {
							alert(result.errors[0].errors[0]);
						}
						// Clear the item now that it's been confirmed updated
						delete dirty[id];
						delete updating[id];
					});
				};
			}
			
			// For every dirty item, grab the ID
			for(var id in dirty) {
				// Create put function to handle the saving of the the item
				var put = putter(id, dirty[id]);
				
				// Add this item onto the promise chain,
				// getting the item from the store first if desired.
				promise = promise.then(getFunc(id)).then(put);
			}
			promise = promise.then(function(){self.refresh()});
			
			// Kick off and return the promise representing all applicable get/put ops.
			// If the success callback is fired, all operations succeeded; otherwise,
			// save will stop at the first error it encounters.
			dfd.resolve();
			return promise;
		},
		filterChange:function(node) {
			var name = (typeof node['get'] == "undefined") ? attr.get(node, 'name') : node.get('name');
			var value = (typeof node['get'] == "undefined") ? attr.get(node, 'value') : node.get('value');
			if (typeof value == "object" && typeof node['serialize'] == "function") {
				value = node.serialize(value);
			}
			this.query[name] = value;
			this.set('query', this.query);
		}
});
return Grid;
});

