define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/query",
	"dojo/on",
	"dijit/_WidgetBase",
	"dojo/dom-attr",
	"dijit/registry",
	"dojo/ready"
], function(declare, lang, query, on, Widget, attr, registry, ready){
	return declare([Widget], {
		key:false,
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
			ready(lang.hitch(this, function() {
				query('[data-depend='+this.key+']').forEach(lang.hitch(this, function(node) {
					this.dependents.push(registry.byNode(node));
				}));
				this.execute();
			}));
		},
		execute: function() {
			this.value = attr.get(this.domNode, 'value');
			for (var i in this.dependents) {
				this.dependents[i].toggleDependency(this.get('value'), this);
			}
		}
	});
});
