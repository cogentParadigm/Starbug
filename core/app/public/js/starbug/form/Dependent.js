define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/query",
	"dijit/_WidgetBase",
	"dojo/NodeList-traverse",
	"dojo/NodeList-dom"
], function(declare, lang, query, Widget){
	return declare([Widget], {
		clearDisabled: true,
		postMixInProperties: function() {
			this.inherited(arguments);
			this.values = this.values || {};
			this.foundValues = {};
			Object.keys(this.values).forEach((function(key) {
				this.foundValues[key] = false;
			}).bind(this));
			this.operators = {
				"IN": function(values, dependency) {
					dependencies = String(dependency).split(",");
					for (var i in dependencies) {
						if (values.indexOf(dependencies[i]) > -1) {
							return true;
						}
					}
					return false;
				},
				"NOT IN": function(values, dependency) {
					dependencies = String(dependency).split(",");
					for (var i in dependencies) {
						if (values.indexOf(dependencies[i]) > -1) {
							return false;
						}
					}
					return true;
				}
			};
		},
		disable: function() {
			query(this.domNode.parentNode).closest('.form-group').addClass("hidden");
			query('input, select, textarea', this.domNode.parentNode).attr('disabled', 'disabled');
			//this.domNode.setAttribute('disabled', 'disabled');
		},
		enable: function() {
			query(this.domNode.parentNode).closest('.form-group').removeClass("hidden");
			query('input, select, textarea', this.domNode.parentNode).removeAttr('disabled');
			//this.domNode.removeAttribute('disabled');
		},
		toggleDependency: function(dependency, widget) {
			if (typeof this.values[widget.key] != "undefined") {
				var values = this.values[widget.key];
				var operator = "IN";
				if (typeof values.length == "undefined") {
					if (typeof values.operator != "undefined") {
						operator = values.operator;
					}
					values = values.values;
				}
				this.foundValues[widget.key] = this.operators[operator](values, dependency);

				var total = Object.keys(this.values).length;
				var valid = Object.values(this.foundValues).filter(Boolean).length;
				if (valid == total) {
					this.enable();
					return true;
				} else {
					if (this.clearDisabled) {
						this.set("value", "");
					}
					this.disable();
					return false;
				}
			}
		}
	});
});
