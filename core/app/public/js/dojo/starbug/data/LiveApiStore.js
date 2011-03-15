dojo.provide("starbug.data.LiveApiStore");
dojo.require("starbug.data.ApiStore");
dojo.require("dojox.dtl.filter.strings");
dojo.declare('starbug.data.LiveApiStore', starbug.data.ApiStore, {
	refreshInterval: 3,
	timer: null,
	onUpdate: null,
	onItem: null,
	onChange: null,
	onDelete: null,
	startTime: '',
	lastPoll: null,
	lastAttempt: null,
	ofsett: null,
	reloads: 0,
	constructor: function(keywordParameters) {
		//SET PROPERTIES
		if (keywordParameters.startTime) this.startTime = keywordParameters.startTime;
		if (keywordParameters.refreshInterval) this.refreshInterval = keywordParameters.refreshIterval;
		if (keywordParameters.onItem) this.onItem = keywordParameters.onItem;
		if (keywordParameters.onUpdate) this.onUpdate = keywordParameters.onUpdate;
		if (keywordParameters.onChange) this.onChange = keywordParameters.onChange;
		if (keywordParameters.onDelete) this.onDelete = keywordParameters.onDelete;

		//SET lastPoll AND offset FOR UPDATE REQUESTS
		var t = this.startTime.split(/[- :]/);
		this.lastPoll = new Date(t[0], parseInt(t[1])-1, t[2], t[3], t[4], t[5]);
		this.lastAttempt = new Date();
		this.offset = this.lastPoll.getTime() - this.lastAttempt.getTime();

		//START UPDATE TIMER
		if (this.refreshInterval > 0) {
			this.timer = setTimeout(dojo.hitch(this, 'update'), this.refreshInterval*1000);
		}
	},
	update: function(xhrData) {
		clearTimeout(this.timer);
		if (this.isDirty()) {
			this.save();
			return;
		}
		this.lastAttempt = new Date();
		var xhrArgs = {
			url: this.url+"&log="+dojox.dtl.filter.strings.urlencode("log.created>'"+dojo.date.locale.format(this.lastPoll, {datePattern: 'yyyy-MM-dd', timePattern: 'HH:mm:ss'})+"'")+'&select=log.*',
			handleAs: 'json',
			load: dojo.hitch(this, 'onItems')
		};
		if (xhrData != null) dojo.mixin(xhrArgs, xhrData);
		dojo.xhrPost(xhrArgs);
		setTimeout(dojo.hitch(this, 'update'), this.refreshInterval*1000);
	},
	onItems: function(data) {
		this.lastPoll.setTime(this.lastAttempt.getTime()+this.offset);
		if (data) { //UPDATES EXIST
			for (var item in data.items) {
				item = data.items[item];
				if (item.action == "INSERT") {
					dojo.xhrGet({
						url: this.url+'&where='+this.model+'.id='+item.object_id,
						handleAs: 'json',
						load: dojo.hitch(this, function(data) {
							this.addItem(data.items[0]);
							if (this.onItem != null) this.onItem(this._getItemByIdentity(item.object_id), this);
						})
					});
				} else if (item.action == "UPDATE") {
					this.updateValue(this._getItemByIdentity(item.object_id), item.column_name, item.new_value);
					if (this.onChange != null) this.onChange(this._getItemByIdentity(item.object_id), this);
				} else if (item.action == "DELETE") {
					var i = this._getItemByIdentity(item.object_id);
					this.removeItem(i);
					if (this.onDelete != null) this.onDelete(i, this);
				}
			}
			this.reloads++;
			if (this.onUpdate != null) this.onUpdate(this);
		}
	}
});