define(["dojo/_base/declare", "dojo/_base/lang", "dojo/_base/array", "dojo/request/xhr", "dojo/query", "dojo/dom-class", "dojo/dom-style", "dijit/Dialog", "dijit/registry"], function(declare, lang, array, xhr, query, domclass, domstyle, Dialog, registry) {
	return declare([Dialog], {
		dialog:null,
		url:'cart/add',
		callback:null,
		item_id: 0,
		format:"xhr",
		doLayout:false,
		layoutParams:{},
		showTitle:false,
		draggable:false,
		get_data:{},
		post_data:{},
		postCreate: function() {
			this.inherited(arguments);
			var layoutDefaults = {width:'300px', height:'350px', position:'fixed', top:'0', left:'0', right:'0', bottom:'0', margin:'auto', overflow:'auto'};
			for (var i in layoutDefaults) if (typeof this.layoutParams[i] == "undefined") this.layoutParams[i] = layoutDefaults[i];
			domstyle.set(this.domNode, this.layoutParams);
			domstyle.set(this.titleBar, 'display', 'none');
			this.set('content', '');
		},
		_position: function() {},
		resize: function(dim) {
			/*
			 * EXPERIMENTAL SCROLL BEHAVIOR - TO ENABLE, REMOVE overflow:auto FROM THE DIALOG
			if (this.containerNode.clientHeight > this.domNode.clientHeight) {
				var max = this.containerNode.clientHeight -this.domNode.clientHeight;
				domstyle.set(this.containerNode, 'top', 0 - Math.min(window.scrollY, max) + 'px');
			}
			*/
			this._layoutChildren();
		},
		add: function(product, options) {
			this.show();
			var request_url = WEBSITE_URL + this.url+'.xhr';
			var token = '?';
			this.get_data.id = product;
			for (var i in this.get_data) {
				request_url += token+i+'='+this.get_data[i];
				token = '&';
			}
			xhr(request_url).then(lang.hitch(this, 'load'));
		},
		hide: function(evt) {
			if (evt) evt.preventDefault();
			this.inherited(arguments);
		},
		load: function(data) {
			array.forEach(registry.findWidgets(this.domNode), function(w) {
				w.destroyRecursive();
			});
			this.set('content', data);
			query('.hide-dialog', this.domNode).attr('onclick', '').on('click', lang.hitch(this, 'hide'));
		}
	});
});
