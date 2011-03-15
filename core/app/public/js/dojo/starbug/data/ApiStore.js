dojo.provide("starbug.data.ApiStore");
dojo.require("dojo.data.ItemFileWriteStore");
dojo.declare('starbug.data.ApiStore', dojo.data.ItemFileWriteStore, {
	model : '',
	models : '',
	action : 'create',
	query : '',
	changes : [],
	constructor: function(keywordParameters) {
		this.model = keywordParameters.model;
		if (keywordParameters.models) this.models = keywordParameters.models;
		else this.models = this.model;
		if (keywordParameters.query) this.action = keywordParameters.query;
		if (keywordParameters.action) this.action = keywordParameters.action;
		if (keywordParameters.url) this.url = keywordParameters.url;
		this.url = WEBSITE_URL+'api/'+this.models+'/get.json?query='+this.query;
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