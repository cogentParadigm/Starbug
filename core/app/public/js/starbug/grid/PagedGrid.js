define([
	"dojo",
	"dojo/_base/lang",
	"dojo/on",
	"starbug",
	"sb",
	"sb/data/Query",
	"sb/store/Api",
	"dgrid/GridFromHtml",
	"dgrid/Grid",
	"dgrid/Keyboard",
	"dgrid/Selector",
	"dgrid/extensions/Pagination",
	"dgrid/Editor",
	"dojo/_base/Deferred",
	"dojo/dom-attr"
], function(dojo, lang, on, starbug, sb, Query, Api, GridFromHtml, List, Keyboard, Selector, Pagination, Editor, Deferred, attr){
return dojo.declare('starbug.grid.PagedGrid', [GridFromHtml, List, Keyboard, Selector, Pagination, Editor], {
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
	postCreate: function() {
		this.query = this.query || {};
		this.query = new Query({scope: this.model, saveScope: this.model + "Query", query: this.query});
		this.query.on("change", lang.hitch(this, function(e) {
			this.set('collection', this.collection.root.filter(e.query));
		}));
		this.query.on("reset", lang.hitch(this, function() {
			if (this.query.read("currentPage")) {
				this.query.remove("currentPage");
				this.gotoPage(1);
			}
		}));
		if (this.model && this.action) {
			this.collection = (new Api({model:this.model, action:this.action})).filter(this.query.query);
		}
		if (this.query.read('rowsPerPage')) {
			this.rowsPerPage = parseInt(this.query.read('rowsPerPage'));
		}
	},
	startup: function() {
		this.inherited(arguments);
		for (var i in this.columns) if (typeof this.columns[i]['editor'] != "undefined") this.columns[i].autoSave = true;
		if (this.query.read("currentPage")) {
			this.gotoPage(this.query.read("currentPage"));
		}
	},
	_setRowsPerPage: function(value) {
		localStorage.setItem('rowsPerPage', value);
		this.inherited(arguments);
	},
	_updateNavigation: function() {
		this.inherited(arguments);
		if (this._currentPage != this.query.read("currentPage")) {
			this.query.save('currentPage', this._currentPage);
		}
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
		}
});
});
