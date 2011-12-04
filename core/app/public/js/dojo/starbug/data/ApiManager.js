define([
	"dojo",
	"dojo/date/locale",
	"dojox/socket",
	"dojox/socket/Reconnect"
], function (dojo, locale, socket, reconnect) {
	var apimanager = dojo.declare("starbug.data.ApiManager", null, {

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
			if (!store.objectStore) this.pending.push(store);
			if (this.hasFetched && (this.fetching.length == 0)) this.fetch();
			if (dojo.config.notifier) this.subscribe(this.stores.length-1);
			if (store.refreshInterval != 0) this.isLive = true;
		},

		fetch: function() {
			if (this.pending.length > 0) {
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
			}
		},

		populate: function(data) {
			for (var i in data) if (!dojo.isArray(data[i])) this.fetching[i]._onData(data[i]);
			this.fetching = [];
			if (this.isLive && (this.timer == null)) this.timer = setTimeout(dojo.hitch(this, 'update'), 1000);
			if (this.pending.length > 0) this.fetch();
		},
		
		subscribe: function(idx) {
			this.notifier = dojo.config.notifier;
			var calls = {};
			if (this.stores[idx].isDirty()) this.stores[idx].save();
			calls[idx] = this.stores[idx].query;
			var sub = dojo.toJson({"subscribe":calls});
			if (this.socket == null) {
				var socketMessageHeader = '~m~49~m~'; 
				var args, ws = typeof WebSocket != 'undefined';
				var socketSessionId;
				this.socket = socket(args = {
					url: ws ? this.notifier+'/socket.io/websocket' : this.notifier+'/socket.io/xhr-polling',
					headers: {'Content-Type':'application/x-www-urlencoded'},
					transport: function (args, message) {
						args.content = message;
						dojo.xhrPost(args);
					}
				});
				this.socket = reconnect(this.socket);
				this.socket.manager = this;
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
			} else {
				this.socket.on("open", function(event) {
					this.send('~m~'+(sub.length)+'~m~'+sub);
				});
			}
		},

		update: function(xhrData) {
			clearTimeout(this.timer);
			this.reloads++;
			var args = {url: WEBSITE_URL+'api/call.json', handleAs: 'json', content: {}, load: dojo.hitch(this, 'onUpdate')};
			for (var i in this.stores) {
				if (this.stores[i].isDirty()) this.stores[i].save();
				if ((this.reloads % this.stores[i].refreshInterval) === 0) {
					this.stores[i].lastAttempt = new Date();
					args.content['calls['+i+']'] = this.stores[i].models+'  '+this.stores[i].query+"  log:log.created>'"+locale.format(this.stores[i].lastPoll, {datePattern: 'yyyy-MM-dd', timePattern: 'HH:mm:ss'})+"'  select:log.*";
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
						for (var item in data[i]) {
							item = data[i][item];
							console.log(item);
							if (item.action == "INSERT") {
								var args = {};
								args['calls['+i+']'] = this.stores[i].query+'  where:'+this.stores[i].model+'.id='+item.object_id;
								dojo.xhrPost({
									url: WEBSITE_URL+'api/call.json',
									content: args,
									handleAs: 'json',
									load: dojo.hitch(this, function(data) {
										this.stores[i].addItem(data[i][0]);
									})
								});
							} else if (item.action == "UPDATE") {
								this.stores[i].fetchItemByIdentity({
									identity: item.object_id,
									onItem: dojo.hitch(this, function(item) {
										if (this.stores[i].isItem(item)) this.stores[i].updateValue(item, item.column_name, item.new_value);
									})
								});
							} else if (item.action == "DELETE") {
								this.stores[i].fetchItemByIdentity({
									identity: item.object_id,
									onItem: dojo.hitch(this, function(item) {
										this.stores[i].removeItem(item);
									})
								});
							}
						}
						if (this.stores[i].refreshInterval) this.stores[i].reloads++;
						if (this.stores[i].onUpdate != null) this.stores[i].onUpdate(this.stores[i]);
					}
				}
			}
		}
	});
	var instance = new apimanager();
	return function() { return instance; }
});
