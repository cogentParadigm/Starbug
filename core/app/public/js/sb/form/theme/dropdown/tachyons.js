define(["dojo/_base/declare", "sb/css/Theme", "put-selector/put"], function(declare, Theme, put) {
  var theme = new (declare([Theme], {
    createControlGroupNode: function() {
      this.controlGroupNode = put(this.domNode, "+div.dropdown-indicator");
      if (typeof this.themeOptions != "undefined" && typeof this.themeOptions.size != "undefined") {
        put(this.controlGroupNode, ".dropdown-indicator-" + this.themeOptions.size);
      }
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'input.form-control[type=text][autocomplete=off][readonly]');
      if (this.domNode.getAttribute("placeholder")) {
        this.controlNode.setAttribute("placeholder", this.domNode.getAttribute("placeholder"));
      }
      if (typeof this.themeOptions != "undefined" && typeof this.themeOptions.size != "undefined") {
        put(this.controlNode, ".form-control-" + this.themeOptions.size);
      }
    },
    createRootNode: function() {
      this.rootNode = this.domNode.parentNode;
    },
    createToggleNode: function() {
      return false;
    },
    createDropdownNode: function() {
      this.dropdownNode = put(this.rootNode, 'div.bg-white.br2.br--bottom.shadow-4.hidden');
    }
  }))();
  return theme;
});