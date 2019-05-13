define([
  "dojo/_base/declare",
  "./Select",
  "put-selector/put"
], function (declare, Select, put) {
  return declare([Select], {
    searchable: true,
    searchThreshold: 2,
    createControlGroupNode: function() {
      this.controlGroupNode = put(this.domNode, "+div");
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'input[type=text][autocomplete=off].form-control');
      if (this.domNode.getAttribute("placeholder")) {
        this.controlNode.setAttribute('placeholder', this.domNode.getAttribute('placeholder'));
      }
    },
    createInputNode: function() {
      this.inputNode = this.controlNode;
    },
    createToggleNode: function() {
      // remove the toggle node.
    },
    close: function() {
      this.inherited(arguments);
      this.inputNode.value = this.query.query[this.filterAttrName] = this.get("displayedValue");
    },
    refresh: function() {
      this.inherited(arguments);
      this.inputNode.value = this.query.query[this.filterAttrName] = this.get("displayedValue");
    },
    onFocus: function() {
      // disable focus behavior.
    }
  });
});
