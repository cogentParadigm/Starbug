dojo.provide("starbug.base");
dojo.require("dijit.form.Form");
dojo.require("dijit.form.TextBox");
dojo.declare("starbug.base", null, {
	severTime: '',
	$_GET:[],
	constructor: function() {
		this.serverTime = dojo.config.serverTime;
		dojo.addOnLoad(dojo.hitch(this, 'onload'));
		var urlHalves = String(document.location).split('?');
		if(urlHalves[1]){
			var urlVars = urlHalves[1].split('&');
			for(var i=0; i<=(urlVars.length); i++){
				 if(urlVars[i]){
						var urlVarPair = urlVars[i].split('=');
						this.$_GET[urlVarPair[0]] = urlVarPair[1];
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
	query: function(query, args) {
		this.require("data.ApiStore");
		if (!args) args = {};
		args.query = query;
		return new starbug.data.ApiStore(args);
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
	replace : function(data) {
		if (data.args.node.constructor.toString().indexOf('Array') == -1) data.args.node = [data.args.node];
		for(var i in data.args.node) {
			var node = (typeof(data.args.node[i]) == "string") ? dojo.query(data.args.node[i])[0] : data.args.node[i];
			node.innerHTML = data.args.data;
		}
	},
	append : function(data) {
		var node = dojo.query(data.args.node)[0];
		node.innerHTML += data.args.data;
	},
	prepend : function(data) {
		var node = dojo.query(data.args.node)[0];
		node.innerHTML = data.args.data + node.innerHTML;
	},
	destroy : function(data) {
		if (data.args.node.constructor.toString().indexOf('Array') == -1) data.args.node = [data.args.node];
		for(var i in data.args.node) {
			var node = (typeof(data.args.node[i]) == "string") ? dojo.query(data.args.node[i])[0] : data.args.node[i];
			node.parentNode.removeChild(node);
		}
	},
	toggle : function(data) {
		var node = (typeof(data.args.node) == "string") ? dojo.query(data.args.node)[0] : data.args.node;
		var toggler = data.args.toggler;
		var display = dojo.attr(node, 'displayed');
		if (display == 'on') {
			toggler.hide();
			display = 'off';
		} else {
			toggler.show();
			display = 'on';
		}
		dojo.attr(node, 'displayed', display);
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
var sb = new starbug.base();
