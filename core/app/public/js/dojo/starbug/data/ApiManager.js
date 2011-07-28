dojo.provide("starbug.data.ApiManager");
dojo.require("dojo.date.locale");
dojo.require("dojox.socket");
dojo.require("dojox.socket.Reconnect");
dojo.declare("starbug.data.ApiManager", null, {

	stores: [],
	pending: [],
	fetching: [],
	reloads: 0,
	timer: null,
	isLive: false,
	socket:null,
	hasFetched:false,

	constructor: function() {
		dojo.addOnLoad(dojo.hitch(this, 'fetch'));
	},

	register: function(store) {
		this.stores.push(store);
		this.pending.push(store);
		if (this.hasFetched && (this.fetching.length == 0)) this.fetch();
		if (store.refreshInterval != 0) this.isLive = true;
	},

	fetch: function() {
		this.fetching = this.pending;
		this.pending = [];
		this.hasFetched = true;
		var args = {};
		for (var i in this.fetching) args['calls['+i+']'] = this.fetching[i].query;
		dojo.xhrPost({
			url: WEBSITE_URL+'api/call.json',
			handleAs: 'json',
			content: args,
			load: dojo.hitch(this, 'populate')
		});
		if ((this.socket == null) && (dojo.config.notifier)) this.subscribe(dojo.config.notifier);
	},

	populate: function(data) {
		for (var i in data) if (!dojo.isArray(data[i])) this.fetching[i]._onData(data[i]);
		this.fetching = [];
		if (this.isLive && (this.timer == null)) this.timer = setTimeout(dojo.hitch(this, 'update'), 1000);
		if (this.pending.length > 0) this.fetch();
	},
	
	subscribe: function(notifier) {
		this.notifier = notifier;
		var calls = [];
		for (var i in this.stores) {
			if (this.stores[i].isDirty()) this.stores[i].save();
			calls.push(this.stores[i].query);
		}
		var socketMessageHeader = '~m~49~m~'; 
		var args, ws = typeof WebSocket != 'undefined';
		var socketSessionId;
		this.socket = dojox.socket(args = {
			url: ws ? this.notifier+'/socket.io/websocket' : this.notifier+'/socket.io/xhr-polling',
			headers: {'Content-Type':'application/x-www-urlencoded'},
			transport: function (args, message) {
				args.content = message;
				dojo.xhrPost(args);
			}
		});
		this.socket = dojox.socket.Reconnect(this.socket);
		this.socket.manager = starbug.data.manager();
		var sub = dojo.toJson({"subscribe":calls});
		this.socket.on("open", function(event) {
			console.log('client connected');
			this.send('~m~'+(sub.length)+'~m~'+sub);
		}, false, false);
		this.socket.on("message", function(event) {
			var message = event.data.split('~m~');
			message = message[message.length-1];
			if (!socketSessionId){
				socketSessionId = event.data;
				args.url += '/' + socketSessionId;
			}else if(message.substr(0, 3) == '~h~'){
				console.log('heartbeat received');
				this.send(event.data);
			} else {
				console.log(message);
				this.manager.onUpdate(dojo.fromJson(message));
			}
		}, false, false);
	},

	update: function(xhrData) {
		clearTimeout(this.timer);
		this.reloads++;
		var args = {url: WEBSITE_URL+'api/call.json', handleAs: 'json', content: {}, load: dojo.hitch(this, 'onUpdate')};
		for (var i in this.stores) {
			if (this.stores[i].isDirty()) this.stores[i].save();
			if ((this.reloads % this.stores[i].refreshInterval) === 0) {
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
				if (this.stores[i].refreshInterval) this.stores[i].lastPoll.setTime(this.stores[i].lastAttempt.getTime()+this.stores[i].offset);
				if (data[i] != false) { //UPDATES EXIST
					for (var item in data[i].items) {
						item = data[i].items[item];
						console.log(item);
						if (item.action == "INSERT") {
							var args = {};
							args['calls['+i+']'] = this.stores[i].query+'  where:'+this.stores[i].model+'.id='+item.object_id;
							dojo.xhrPost({
								url: WEBSITE_URL+'api/call.json',
								content: args,
								handleAs: 'json',
								load: dojo.hitch(this, function(data) {
									this.stores[i].addItem(data[i].items[0]);
									if (this.stores[i].onItem != null) this.stores[i].onItem(this.stores[i]._getItemByIdentity(item.object_id), this.stores[i]);
								})
							});
						} else if (item.action == "UPDATE") {
							if (this.stores[i].isItem(this.stores[i]._getItemByIdentity(item.object_id))) {
								this.stores[i].updateValue(this.stores[i]._getItemByIdentity(item.object_id), item.column_name, item.new_value);
								if (this.stores[i].onChange != null) this.stores[i].onChange(this.stores[i]._getItemByIdentity(item.object_id), this.stores[i]);
							}
						} else if (item.action == "DELETE") {
							var target = this.stores[i]._getItemByIdentity(item.object_id);
							this.stores[i].removeItem(target);
							if (this.stores[i].onDelete != null) this.stores[i].onDelete(i, this.stores[i]);
						}
					}
					if (this.stores[i].refreshInterval) this.stores[i].reloads++;
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
