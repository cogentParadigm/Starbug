dojo.provide("starbug.data.ItemFileWriteStore");
dojo.require("dojo.data.ItemFileWriteStore");
dojo.declare('starbug.data.ItemFileWriteStore', dojo.data.ItemFileWriteStore, {
	model : '',
	action : 'create',
	changes : [],
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
						if (typeof values[0] == "object") values[0] = dojo.date.stamp.toISOString(values[0], {selector: 'date'})+' 00:00:00'
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
	}
});