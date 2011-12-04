define([
	"dojo",
	"dojo/data/ItemFileWriteStore",
	"starbug/data/ApiManager"
], function (dojo, writestore, apimanager) {
	return dojo.declare('starbug.data.ApiStore', writestore, {

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
		startTime: '',
		lastPoll: null,
		lastAttempt: null,
		ofsett: null,
		reloads: 0,
		notifier: '',
		constructor: function(args) {
			args.data = {identifier:'id', items:[]};
			this.manager = apimanager();
			dojo.mixin(this, args);
			this.models = this.query.split('  ', 1)[0];
			this.model = this.models.split('.', 1)[0];

			//SET lastPoll AND offset FOR UPDATE REQUESTS VIA POLLING
			if (this.refreshInterval != 0) {
				var t = this.startTime.split(/[- :]/);
				this.lastPoll = new Date(t[0], parseInt(t[1])-1, t[2], t[3], t[4], t[5]);
				this.lastAttempt = new Date();
				this.offset = this.lastPoll.getTime() - this.lastAttempt.getTime();
			}

			this.manager.register(this);
		},

		_onData: function(data) {
			this.data = data;
			this.clearOnClose = true;
			this.close();
			var args = {};
			if (this.onItem) args.onItem = this.onItem;
			if (this.onComplete) args.onComplete = this.onComplete;
			if (this.onError) args.onError = this.onError;
			this.fetch(args);
		},

		update: function() {
			this.manager.update();
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
				dojo.xhrPost({url: WEBSITE_URL+'api/'+this.model+'.json', content: i, handleAs: 'json'});
			}
			for (var i in changeSet._deletedItems) {
				var d = {};
				d[this.model+'[id]'] = i;
				d['action['+this.model+']'] = 'delete';
				dojo.xhrPost({url:	WEBSITE_URL+'api/'+this.model+'.json', content: d});
			}
			for (var i in changeSet._newItems) {
				console.log(i);
			}
			saveComplete();
		},
		_setValueOrValues: function(/* item */ item, /* attribute-name-string */ attribute, /* anything */ newValueOrValues, /*boolean?*/ callOnSet, noSave) {
			var success = this.inherited(arguments);
			if (success && !noSave) this.save();
			return success;
		},
		
		deleteItem: function(item, noSave) {
			var success = this.inherited(arguments);
			if (success && !noSave) this.save();
			return success;
		},
		
		newItem: function (/* Object? */ keywordArgs, /* Object? */ parentInfo, noSave) {
			var item = this.inherited(arguments);
			if (item && !noSave) this.save();
			return item;
		},

		updateValue: function(/* item */ item, /* attribute-name-string */ attribute, /* almost anything */ value){
			// summary: See dojo.data.api.Write.set()
			var success = this._setValueOrValues(item, attribute, value, true, true); // boolean
			if (success) delete this._pending._modifiedItems[this.getIdentity(item)];
			return success;
		},

		updateValues: function(/* item */ item, /* attribute-name-string */ attribute, /* array */ values){
			// summary: See dojo.data.api.Write.setValues()
			var success = this._setValueOrValues(item, attribute, values, true, true); // boolean
			if (success) delete this._pending._modifiedItems[this.getIdentity(item)];
			return success;
		},

		removeItem: function(/* item */ item) {
			var success = this.deleteItem(item, true);
			if (success) delete this._pending._deletedItems[this.getIdentity(item)];
			return success;
		},

		addItem: function(/* Object? */ keywordArgs, /* Object? */ parentInfo){
			var newItem = this.newItem(keywordArgs, parentInfo, true);
			if (newItem != null) delete this._pending._newItems[this.getIdentity(keywordArgs['id'])];
			return newItem;
		}

	});
});
