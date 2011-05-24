dojo.provide("starbug.data.ApiManager");
dojo.require("dojo.date.locale");
dojo.declare("starbug.data.ApiManager", null, {

	stores: [],
	pending: [],
	reloads: 0,
	timer: null,
	isLive: false,

	constructor: function() {
		dojo.addOnLoad(dojo.hitch(this, 'fetch'));
	},

	register: function(store) {
		this.stores.push(store);
		this.pending.push(store);
		if (store.updateInterval) this.isLive = true;
	},

	fetch: function() {
		var args = {};
		for (var i in this.pending) args['calls['+i+']'] = this.pending[i].models+'  '+this.pending[i].query;
		dojo.xhrPost({
			url: WEBSITE_URL+'api/call.json',
			handleAs: 'json',
			content: args,
			load: dojo.hitch(this, 'populate')
		});
	},

	populate: function(data) {
		for (var i in data) {
			this.pending[i]._onData(data[i]);
			if (data[i].items) {
				for (var d in data[i].items) {
					dojo.publish('/data/'+this.stores[i].models+'/add', this.stores[i]._getItemByIdentity(data[i].items[d].id));
				}
			}
		}
		this.pending = [];
		if (this.isLive && (this.timer == null)) this.timer = setTimeout(dojo.hitch(this, 'update'), 1000);
	},

	update: function(xhrData) {
		clearTimeout(this.timer);
		this.reloads++;
		var args = {url: 'api/call.json', handleAs: 'json', content: {}, load: dojo.hitch(this, 'onUpdate')};
		for (var i in this.stores) {
			if (this.stores[i].isDirty()) this.stores[i].save();
			if ((this.reloads % this.stores[i].updateInterval) === 0) {
				this.stores[i].lastAttempt = new Date();
				args.content['calls['+i+']'] = this.stores[i].models+'  '+this.stores[i].query+"  log:log.created>'"+dojo.date.locale.format(this.stores[i].lastPoll, {datePattern: 'yyyy-MM-dd', timePattern: 'HH:mm:ss'})+"'  select:log.*";
			}
		}
		if (xhrData != null) dojo.mixin(args, xhrData);
		var empty = true;
		for (var prop in args.content) if (args.content.hasOwnProperty(prop)) empty = false;
		if (!empty) dojo.xhrPost(args);
		this.timer = setTimeout(dojo.hitch(this, 'update'), 1000);
	},

	onUpdate: function(data) {
		if (data) {
			for (var i in data) {
				this.stores[i].lastPoll.setTime(this.stores[i].lastAttempt.getTime()+this.stores[i].offset);
				if (data[i] != false) { //UPDATES EXIST
					for (var item in data[i].items) {
						item = data[i].items[item];
						if (item.action == "INSERT") {
							var args = {};
							args['calls['+i+']'] = this.stores[i].models+'  '+this.stores[i].query+'  where:'+this.stores[i].model+'.id='+item.object_id;
							dojo.xhrPost({
								url: 'api/call.json',
								content: args,
								handleAs: 'json',
								load: dojo.hitch(this, function(data) {
									this.stores[i].addItem(data[0].items[0]);
									if (this.stores[i].onItem != null) this.stores[i].onItem(this.stores[i]._getItemByIdentity(item.object_id), this.stores[i]);
									dojo.publish('/data/'+this.stores[i].models+'/add', this.stores[i]._getItemByIdentity(item.object_id));
								})
							});
						} else if (item.action == "UPDATE") {
							this.stores[i].updateValue(this.stores[i]._getItemByIdentity(item.object_id), item.column_name, item.new_value);
							if (this.stores[i].onChange != null) this.stores[i].onChange(this.stores[i]._getItemByIdentity(item.object_id), this.stores[i]);
						} else if (item.action == "DELETE") {
							var target = this.stores[i]._getItemByIdentity(item.object_id);
							this.stores[i].removeItem(target);
							if (this.stores[i].onDelete != null) this.stores[i].onDelete(i, this.stores[i]);
						}
					}
					this.stores[i].reloads++;
					if (this.stores[i].onUpdate != null) this.stores[i].onUpdate(this.stores[i]);
				}
			}
		}
	}

});

// The manager singleton variable. Can be overwritten if needed.
starbug.data._manager = null;

starbug.data.manager = function(){
	// Returns the current Api Manager.  Creates one if it is not created yet.
	if (!starbug.data._manager) starbug.data._manager = new starbug.data.ApiManager();
	return starbug.data._manager;
};