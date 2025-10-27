define([
  "dojo/_base/declare",
  "dijit/_WidgetBase"
], function(declare, Widget) {
  return declare([Widget], {
    postCreate: function() {
      this.inherited(arguments);
      this._setValueAttr = this._setValueAttrHandler;
    },
    _getValueAttr: function() {
      return (this.domNode.checked) ? this._get('value') : 0;
    },
    _setValueAttrHandler: function(value) {
      this.domNode.checked = this.domNode.value == value;
    }
  });
});
