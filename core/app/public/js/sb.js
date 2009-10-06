var sb = {};
dojo.mixin(sb, {
	xhr : function(data) {
		if (data.args.confirm && !confirm(data.args.confirm)) return;
		var xhr_object = {
			load: function(response, xhr) {
				xhr.args.data = response;
				data.args.action(xhr);
				dojo.behavior.apply();
			}
		}
		dojo.mixin(xhr_object, data.args);
		if (data.args.method == "post") dojo.xhrPost(xhr_object);
		else dojo.xhrGet(xhr_object);
	},
	replace : function(data) {
		if (typeof(data.args.node) == 'string') data.args.node = [data.args.node];
		for(var i in data.args.node) {
			var node = dojo.query(data.args.node[i])[0];
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
	}
});
