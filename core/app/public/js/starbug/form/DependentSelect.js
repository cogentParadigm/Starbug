define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/query",
	"./Dependent",
	"sb/store/Api",
	"dojo/NodeList-traverse",
	"dojo/NodeList-dom"
], function(declare, lang, query, Dependent, Api){
	return declare([Dependent], {
		store:null,
		model:'',
		action:'select',
		toggleOnLoad:true,
		hasLoaded:false,
		query:{},
		paramName:false,
		value:false,
		postCreate:function() {
			this.inherited(arguments);
			this.store = new Api({model:this.model, action:this.action});
			this.paramName = this.paramName || this.key;
		},
		populate: function(value) {
			var self = this;
			this.query[this.paramName] = value;
			query('option', this.domNode).forEach(function(node) {
				if (node.value.length > 0) self.domNode.removeChild(node);
			});
			if (value.length > 0) {
				console.log(value);
				this.enable();
				this.store.filter(this.query).forEach(function(object) {
					var node = document.createElement('option');
					node.value = object.id;
					node.innerHTML = object.label;
					if (self.value && self.value == object.id) node.setAttribute('selected', 'selected');
					self.domNode.appendChild(node);
				});
			} else {
				this.disable();
			}
		},
		toggle:function(dependency) {
			if (this.hasLoaded || this.toggleOnLoad) {
				this.populate(dependency);
			}
			this.hasLoaded = true;
		}
	});
});
