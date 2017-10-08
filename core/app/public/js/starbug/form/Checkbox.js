define([
	"dojo/_base/declare",
	"dijit/_WidgetBase"
], function(declare, Widget) {
	return declare([Widget], {
		_getValueAttr: function() {
			return (this.domNode.checked) ? this._get('value') : 0;
		}
	});
});
