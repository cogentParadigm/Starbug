define([
	"dojo/_base/declare",
	"dgrid/List",
	"put-selector/put"
], function (declare, List, put) {
	return declare([List], {
		keepScrollPosition:true,
		addUiClasses:false,
		renderRow: function(object, options){
			var self = this;
			var label = object.label.length ? object.label : "&nbsp;";
			var node = put('div', {innerHTML: label});
			return node;
		}
	});
});
