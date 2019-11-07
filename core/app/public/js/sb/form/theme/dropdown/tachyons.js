define(["dojo/_base/declare", "sb/css/Theme", "put-selector/put"], function(declare, Theme, put) {
  var theme = new (declare([Theme], {
    createControlGroupNode: function() {
      this.controlGroupNode = put(this.domNode, "+div.dropdown-indicator");
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'button[type=button].tl.form-control', {innerHTML: '&nbsp;'});
      if (this.domNode.getAttribute("placeholder")) {
        put(this.controlNode, {innerHTML: this.domNode.getAttribute('placeholder')});
      }
    },
    createToggleNode: function() {
      return false;
    },
    createDropdownNode: function() {
      this.dropdownNode = put(this.domNode.parentNode, 'div.select-list.hidden');
    }
  }))();
  return theme;
})