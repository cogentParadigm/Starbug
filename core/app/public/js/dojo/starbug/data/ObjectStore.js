dojo.provide("starbug.data.ObjectStore");
dojo.require("starbug.store.Api");
dojo.require("starbug.data.ApiManager");
dojo.require("dojo.data.ObjectStore");
dojo.declare("starbug.data.ObjectStore", dojo.data.ObjectStore, {
	model : '',
	models : '',
	query : '',
	changes : [],
	manager: null,
	onComplete: null,
	onError: null,
	onUpdate: null,
	onItem: null,
	onChange: null,
	constructor:function(options) {
		this.objectStore = new starbug.store.Api(options);
		this.query = this.objectStore.apiQuery;
		this.model = this.objectStore.model;
		this.models = this.objectStore.models;
		this.manager = starbug.data.manager();
		this.manager.register(this);
	},
	setValue: function(item, attribute, value, noSave) {
		var old = item[attribute];
		item[attribute]=value;
		if (!noSave) this.changing(item);
		this.onSet(item,attribute,old,value);
	},
	deleteItem: function(item, noSave){
		if (!noSave) this.changing(item, true);
		this.onDelete(item);
	},
	newItem: function(data, parentInfo, noSave){
		if(parentInfo){
			// get the previous value or any empty array
			var values = this.getValue(parentInfo.parent,parentInfo.attribute,[]);
			// set the new value
			values = values.concat([data]);
			data.__parent = values;
			this.setValue(parentInfo.parent, parentInfo.attribute, values);
		}
		if (!noSave) this._dirtyObjects.push({object:data, save: true});
		this.onNew(data);
		return data;
	},
	updateValue: function(item, attribute, value){
		this.setValue(item, attribute, value, true);
	},
	removeItem: function(item) {
		this.deleteItem(item, true);
	},
	addItem: function(data, parentInfo) {
		var item = this.newItem(data, parentInfo, true);
		return item;
	},
	fetch: function(args){	
		args = args || {};
		var self = this;
		var scope = args.scope || self;
		var query = args.query || {};
		if(typeof query == "object"){ // can be null, but that is ignore by for-in
			query = dojo.delegate(query); // don't modify the original
		}
		var results = this.objectStore.query(query, args);
		dojo.when(results.total, function(totalCount){
			dojo.when(results, function(results){
				if(args.onBegin){
					args.onBegin.call(scope, totalCount || results.length, args);
				}
				if(args.onItem){
					for(var i=0; i<results.length;i++){
						args.onItem.call(scope, results[i], args);
					}
				}
				if(args.onComplete){
					args.onComplete.call(scope, args.onItem ? null : results, args);
				}
				return results;
			}, errorHandler);
		}, errorHandler);
		function errorHandler(error){
			if(args.onError){
				args.onError.call(scope, error, args);
			}
		}
		args.abort = function(){
			// abort the request
			if(results.cancel){
				results.cancel();
			}
		};
		args.store = this;
		return args;
	},
	changing: function(object,_deleting){
		// summary:
		//		adds an object to the list of dirty objects.  This object
		//		contains a reference to the object itself as well as a
		//		cloned and trimmed version of old object for use with
		//		revert.
		console.log(object);
		if (_deleting) result = this.objectStore.remove(this.getIdentity(object));
		else result = this.objectStore.put(object);
	},
	update: function() {

	}
});
