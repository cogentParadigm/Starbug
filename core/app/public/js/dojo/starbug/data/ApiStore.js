dojo.provide("starbug.data.ApiStore");
dojo.require("dojo.data.ItemFileWriteStore");
dojo.require("starbug.data.ApiManager");
dojo.declare('starbug.data.ApiStore', dojo.data.ItemFileWriteStore, {

	model : '',
	models : '',
	action : 'create',
	query : '',
	changes : [],
	manager: null,
	refreshInterval: 0,
	timer: null,
	onComplete: null,
	onError: null,
	onUpdate: null,
	onItem: null,
	onChange: null,
	onDelete: null,
	startTime: '',
	lastPoll: null,
	lastAttempt: null,
	ofsett: null,
	reloads: 0,

	constructor: function(args) {
		this.manager = starbug.data.manager();
		dojo.mixin(this, args);
		this.models = this.query.split('  ', 1)[0];
		this.model = this.models.split('.', 1)[0];

		//SET lastPoll AND offset FOR UPDATE REQUESTS
		if (this.startTime != '') {
			if (this.refreshInterval == 0) this.refreshInterval = 3;
			var t = this.startTime.split(/[- :]/);
			this.lastPoll = new Date(t[0], parseInt(t[1])-1, t[2], t[3], t[4], t[5]);
			this.lastAttempt = new Date();
			this.offset = this.lastPoll.getTime() - this.lastAttempt.getTime();
		}

		this.manager.register(this);
	},

	_onData: function(data) {
		this.data = data;
		this.fetch();
	},

	fetch: function(args) {
		if (!args) args = {};
		if (this.onItem) args.onItem = this.onItem;
		if (this.onComplete) args.onComplete = this.onComplete;
		if (this.onError) args.onError = this.onError;
		this.inherited(arguments);
	},

	itemToJS : function(idx) {
	// summary: Function to convert an item at the specified index into a simple JS object.
		var item = null;
		if (this._itemsByIdentity) item = this._itemsByIdentity[idx];
		else item = this._arrayOfAllItems[idx];
		var js = {};
		if (item) {
			//Determine the attributes we need to process.
			var attributes = this.getAttributes(item);
			if (attributes && attributes.length > 0) {
				var i;
				for (i = 0; i < attributes.length; i++) {
					var values = this.getValues(item, attributes[i]);
					if (values) {
						//simplified to handle only single-valued attributes, full function at dojocampus itemFileWriteStore page
						js[this.model+'['+attributes[i]+']'] = values[0];
					}
				}
			}
		}
		return js;
	},

	_saveCustom : function(saveComplete, saveFailed) {
		//  summary: This is a custom save function for the store to populate an array of modified items that can be submitted to the server
		var changeSet = this._pending;
		for (var i in changeSet._modifiedItems) {
			i = this.itemToJS(i);
			i['action['+this.model+']'] = this.action;
			dojo.xhrPost({url: this.url, content: i, handleAs: 'json'});
		}
		saveComplete();
	},
	_setValueOrValues: function(/* item */ item, /* attribute-name-string */ attribute, /* anything */ newValueOrValues, /*boolean?*/ callOnSet) {
		var success = this.inherited(arguments);
		if (success) this.save();
		return success;
	},

	updateValue: function(/* item */ item, /* attribute-name-string */ attribute, /* almost anything */ value){
		// summary: See dojo.data.api.Write.set()
		var success = this._setValueOrValues(item, attribute, value, true); // boolean
		if (success) delete this._pending._modifiedItems[this.getIdentity(item)];
		return success;
	},

	updateValues: function(/* item */ item, /* attribute-name-string */ attribute, /* array */ values){
		// summary: See dojo.data.api.Write.setValues()
		var success = this._setValueOrValues(item, attribute, values, true); // boolean
		if (success) delete this._pending._modifiedItems[this.getIdentity(item)];
		return success;
	},

	removeItem: function(/* item */ item) {
		var success = this.deleteItem(item);
		if (success) delete this._pending._deletedItems[this.getIdentity(item)];
		return success;
	},

	addItem: function(/* Object? */ keywordArgs, /* Object? */ parentInfo){
		var newItem = this.newItem(keywordArgs, parentInfo);
		if (newItem != null) delete this._pending._newItems[this.getIdentity(keywordArgs['id'])];
		return newItem;
	}

});