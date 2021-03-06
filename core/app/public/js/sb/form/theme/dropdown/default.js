define(["dojo/_base/declare", "sb/css/Theme", "put-selector/put"], function(declare, Theme, put) {
  var theme = new (declare([Theme], {
    createControlGroupNode: function() {
      this.controlGroupNode = put(this.domNode, "+div.input-group");
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'input.form-control[type=text][autocomplete=off][readonly]');
      if (this.domNode.getAttribute("placeholder")) {
        this.controlNode.setAttribute("placeholder", this.domNode.getAttribute("placeholder"));
      }
    },
    createToggleNode: function() {
      this.toggleNode = put(this.controlGroupNode, theme.selector("toggleNode"), theme.text("toggleNode"));
    },
    createDropdownNode: function() {
      this.dropdownNode = put(this.domNode.parentNode, 'div.select-list.hidden');
    }
  }))({
    selectors: {
      toggleNode: "span.input-group-btn button[type=button][tabindex=-1].btn.btn-default"
    },
    content: {
      toggleNode: {innerHTML: '<span class="fa fa-caret-down"></span><span class="sr-only">Toggle Dropdown</span>'}
    }
  });
  return theme;
})