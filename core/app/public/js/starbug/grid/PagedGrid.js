define([
	"dojo",
	"dojo/on",
	"starbug",
	"sb",
	"sb/data",
	"sb/store/Api",
	"dgrid/GridFromHtml",
	"dgrid/Grid",
	"dgrid/Keyboard",
	"dgrid/Selector",
	"dgrid/extensions/Pagination",
	"dgrid/Editor",
	"dojo/_base/Deferred",
	"dojo/dom-attr"
], function(dojo, on, starbug, sb, data, api, GridFromHtml, List, Keyboard, Selector, Pagination, Editor, Deferred, attr){
window.dgrid = window.dgrid || {GridFromHtml:GridFromHtml};
var Grid = dojo.declare('starbug.grid.PagedGrid', [GridFromHtml, List, Keyboard, Selector, Pagination, Editor], {
	pagingLinks: 2,
	firstLastArrows: true,
	previousNextArrows: true,
	pagingTextBox: false,
	pageSizeOptions: [10, 15, 25, 50],
	noDataMessage:'No Results',
	getBeforePut:false,
	query:{},
	deselectOnRefresh:false,
	allowSelectAll:true,
	constructor: function(args) {
		if (args.model && args.action) {
			args.query = args.query || {};
			this.query = args.query;
			this.collection = (new api({model:args.model, action:args.action})).filter(args.query);
		}
		if (localStorage.getItem('rowsPerPage')) {
			this.rowsPerPage = parseInt(localStorage.getItem('rowsPerPage'));
		}
	},
	startup: function() {
		this.inherited(arguments);
		var grid = this;
		for (var i in this.columns) if (typeof this.columns[i]['editor'] != "undefined") this.columns[i].autoSave = true;
		//filter
		on(window.document, '[data-filter='+this.model+']:change,[data-filter='+this.model+']:input', function(e) {
			grid.applyFilterFromInput(e.target);
		});
	},
	_setRowsPerPage: function(value) {
		localStorage.setItem('rowsPerPage', value);
		this.inherited(arguments);
	},
	save: function () {

			// Keep track of the store and puts
			var self = this,
				store = this.collection,
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
			promise = promise.then(function(){self.refresh();});

			// Kick off and return the promise representing all applicable get/put ops.
			// If the success callback is fired, all operations succeeded; otherwise,
			// save will stop at the first error it encounters.
			dfd.resolve();
			return promise;
		},
		applyFilterFromInput:function(node) {
			var name = (typeof node.get == "undefined") ? attr.get(node, 'name') : node.get('name');
			var value = (typeof node.get == "undefined") ? attr.get(node, 'value') : node.get('value');
			if (typeof value == "object" && typeof node.serialize == "function") {
				value = node.serialize(value);
			}
			this.query[name] = value;
			this.set('collection', this.collection.root.filter(this.query));
		}
});
return Grid;
});
