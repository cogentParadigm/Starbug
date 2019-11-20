define(["dojo/_base/declare", "sb/css/Theme", "put-selector/put"], function(declare, Theme, put) {
  var theme = new (declare([Theme], {
    createControlGroupNode: function() {
      this.controlGroupNode = put(this.domNode, "+div.dropdown-indicator");
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'input.form-control[type=text][autocomplete=off][readonly]');
      if (this.domNode.getAttribute("placeholder")) {
        this.controlNode.setAttribute("placeholder", this.domNode.getAttribute("placeholder"));
      }
    },
    createToggleNode: function() {
      return false;
    },
    createDropdownNode: function() {
      this.dropdownNode = put(this.domNode.parentNode, 'div.bg-white.br2.br--bottom.shadow-4.hidden');
    }
  }))();
  return theme;
})