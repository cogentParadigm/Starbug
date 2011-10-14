dojo.provide("starbug.base");
dojo.require("dijit.form.Form");
dojo.require("dijit.form.TextBox");
dojo.declare("starbug.base", null, {
	severTime: '',
	notifier:'',
	$_GET:[],
	stores:{},
	errors:{},
	constructor: function() {
		this.serverTime = dojo.config.serverTime;
		this.notifier = dojo.config.notifier;
		dojo.addOnLoad(dojo.hitch(this, 'onload'));
		window['$_GET'] = [];
		var urlHalves = String(document.location).split('?');
		if(urlHalves[1]){
			var urlVars = urlHalves[1].split('&');
			for(var i=0; i<=(urlVars.length); i++){
				 if(urlVars[i]){
						var urlVarPair = urlVars[i].split('=');
						window['$_GET'][urlVarPair[0]] = urlVarPair[1];
				 }
			}
		}
	},
	onload: function() {
		this.parseForms();
	},
	require: function(module) {
		dojo['require']("starbug."+module);
	},
	star: function(str) {
		var starr = {};
		var pos = null;
		var keypairs = str.split('  ');
		for (var i in keypairs) {
			i = keypairs[i];
			if (-1 != (pos = i.indexOf(':'))) starr[i.substr(0, pos)] = i.substr(pos+1);
		}
		return starr;
	},
	get: function(models) {
		if (typeof this.stores[models] != 'undefined') return this.stores[models];
		this.stores[models] = new starbug.store.Api({apiQuery:models});
		return this.stores[models];
	},
	query: function(models, query) {
		if (!query) query = {};
		if (typeof query == 'string') query = this.star(query);
		return this.get(models).query(query);
	},
	store: function(model, fields) {
		return this.get(model).put(fields).then(dojo.hitch(this, function(data) {
			if (data.errors) {
				if (typeof this.errors[model] == 'undefined') this.errors[model] = {};
				for (var field in data.errors) {
					field = data.errors[field];
					this.errors[model][field.field] = field.errors;
				}
			}
		}));
	},
	xhr : function(data) {
		if (data.args.confirm && !confirm(data.args.confirm)) return;
		var xhr_object = {
			load: function(response, xhr) {
				xhr.args.data = response;
				data.args.action(xhr);
			}
		}
		dojo.mixin(xhr_object, data.args);
		if (data.args.method == "post") dojo.xhrPost(xhr_object);
		else dojo.xhrGet(xhr_object);
	},
	parseForms: function() {
		dojo.query("form").forEach(function (item, idx) {
			//form = new dijit.form.Form({}, item);
			dojo.query("input", item).forEach(function (item, idx) {

			});
			//window[dojo.attr(item, 'id')] = form;
		});
	}
});
window['sb'] = new starbug.base();
