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
			this.listen();
		},
		startup: function() {
			this.inherited(arguments);
			ready(lang.hitch(this, function() {
				query('[data-depend~="'+this.key+'"]').forEach(lang.hitch(this, function(node) {
					this.dependents.push(registry.byNode(node));
				}));
				this.execute();
			}));
		},
		execute: function() {
			this.value = this.getValue();
			for (var i in this.dependents) {
				this.dependents[i].toggleDependency(this.get('value'), this);
			}
		},
		listen: function() {
			if (this.domNode.tagName == "DIV") {
				// For lists of radio buttons or checkboxes.
				on(this.domNode, 'input:change', lang.hitch(this, 'execute'));
			} else {
				// For individual inputs.
				on(this.domNode, 'change', lang.hitch(this, 'execute'));
			}
		},
		getValue: function() {
			if (this.domNode.tagName == "DIV") {
				// For lists of radio buttons or checkboxes.
				return query("input:checked", this.domNode).attr("value")[0];
			} else if (this.domNode.tagName == "INPUT" && (this.domNode.type == "checkbox" || this.domNode.type == "radio")) {
				return this.domNode.checked ? this.domNode.value : 0;
			} else {
				// For individual inputs.
				return attr.get(this.domNode, 'value');
			}
		}
	});
});
