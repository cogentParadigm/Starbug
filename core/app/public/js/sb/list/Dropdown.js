define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"../grid/MultipleSelectionList",
	"dgrid/OnDemandList",
	"put-selector/put",
	"dojo/on"
], function (declare, lang, List, _VirtualScrolling, put, on) {
	return declare([List, _VirtualScrolling], {
		rowHeight: 20,
		delegate:false,
		postCreate: function() {
			this.inherited(arguments);
			this.domNode.style.borderBottom = "1px solid #DDD";
			this.bodyNode.style.maxHeight = "160px";
			this.bodyNode.style.overflow = "auto";
		},
		renderRow: function(object, options) {
			var self = this;
			var label = object.label.length ? {innerHTML: object.label} : {innerHTML: "&nbsp;"};
			var node = put('a.list-group-item.list-group-item-action', label);
			this.delegate.get(object.id).then(function(selected) {
				if (selected) put(node, 'span.pull-right span.fa.fa-check.text-success.green');
				on(node, 'click', function(e) {
					if (!selected) self.delegate.add([object]);
					else if (self.delegate.size != 1) self.delegate.remove(object.id);
				});
			});
			return node;
		}
	});
});
