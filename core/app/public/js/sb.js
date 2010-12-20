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
	open_dialog : function(data) {
		dialog = dijit.byId('dijit_Dialog_'+data.args.dialog);
		if (dialog == null) dialog = new dijit.Dialog({});
		dialog.attr('title', data.args.title);
		dialog.attr('content', data.args.data);
		dojo.parser.parse();
		dialog.show();
	},
	close_dialog : function(args) {
		if (typeof args == "object") var d = args.args.dialog;
		else var d = args;
		dijit.byId('dijit_Dialog_'+d).hide();
		var dlg = dijit.byId('dijit_Dialog_'+d-1);
		if (dlg != null) dialog = dlg;
	},
	post_form : function(data) {
		dojo.query('.error', data.args.form).forEach(dojo.destroy);
		if (data.args.data.items) {
			if (data.args.callback != null) data.args.callback(data.args.data.items, data.args);
			if (data.args.close_dialog != null) {
				sb.close_dialog(data.args.close_dialog);
			}
		} else if (data.args.data.errors) {
			var node = null;
			var span = null;
			for (var field in data.args.data.errors) {
				field = data.args.data.errors[field];
				node = dojo.query('#'+field.field, data.args.form);
				if (node != null) {
					node = node[0];
					for (var e in field.errors) {
						span = '<span class="error">'+field.errors[e]+'</span>';
						dojo.place(span, node, "before");
					}
				}
			}
		}
	}
});
