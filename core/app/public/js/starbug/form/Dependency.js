define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/query",
	"dojo/on",
	"dijit/_WidgetBase",
	"dojo/dom-attr",
	"dijit/registry"
], function(declare, lang, query, on, Widget, attr, registry){
	return declare([Widget], {
		key:false,
		value:false,
		dependents:null,
		constructor: function() {
			this.dependents = [];
		},
		postCreate: function() {
			this.inherited(arguments);
			if (false === this.key) this.key = this.domNode.getAttribute("name");
			on(this.domNode, 'change', lang.hitch(this, 'execute'));
		},
		startup: function() {
			this.inherited(arguments);
			var self = this;
			query('[data-depend='+this.key+']').forEach(function(node) {
				self.dependents.push(registry.byNode(node));
			});
			this.execute();
		},
		execute: function() {
			this.value = attr.get(this.domNode, 'value');
			for (var i in this.dependents) {
				this.dependents[i].toggleDependency(this.value);
			}
		}
	});
});
