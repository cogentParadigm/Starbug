define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/query",
	"dojo/on",
	"dijit/_WidgetBase",
	"dijit/registry"
], function(declare, lang, query, on, Widget, registry){
	return declare([Widget], {
		key:"",
		value:false,
		dependents:null,
		postCreate: function() {
			this.dependents = [];
			on(this.domNode, 'change', lang.hitch(this, 'execute'));
		},
		startup: function() {
			var self = this;
			query('[data-depend='+this.key+']').forEach(function(node) {
				self.dependents.push(registry.byNode(node));
			});
			this.execute();
		},
		execute: function() {
			this.value = false;
			if (this.domNode.options[this.domNode.selectedIndex]) this.value = this.domNode.options[this.domNode.selectedIndex].value;
			for (var i in this.dependents) {
				this.dependents[i].toggle(this.value);
			}
		}
	});
});
