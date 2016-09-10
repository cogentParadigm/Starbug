define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/query",
	"dijit/_WidgetBase",
	"dojo/NodeList-traverse",
	"dojo/NodeList-dom"
], function(declare, lang, query, Widget){
	return declare([Widget], {
		key:"",
		values:[],
		postCreate: function() {
			this.inherited(arguments);
			this.domNode.setAttribute('data-depend', this.key);
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
		toggle:function(dependency) {
			if (this.values.indexOf(dependency) > -1) {
				this.enable();
			} else {
				this.disable();
			}
		}
	});
});
