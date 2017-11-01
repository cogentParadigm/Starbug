define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dgrid/OnDemandList",
  "dgrid/Selection",
  "put-selector/put",
  "dstore/Memory",
  "dojo/on",
  "dojo/dom",
  "dojo/dom-class",
  "dojo/dom-geometry",
  "dojo/ready"
], function (declare, lang, List, Selection, put, Memory, on, dom, domclass, geometry, ready) {
  return declare(null, {
    buildRendering: function() {
      this.inherited(arguments);
      //this.domNode should be a text input with name and value set appropriately
      this.domNode.type = "hidden";
      this.createControlGroup();
      this.createDropdownNode();
    },
    postCreate:function() {
      this.inherited(arguments);
      on(this.toggleNode, 'click', lang.hitch(this, 'toggle'));
      on(this.controlNode, 'click', lang.hitch(this, 'toggle'));
    },
    startup: function() {
      this.inherited(arguments);
      this.addStyles();
    },
    createControlGroup: function() {
      this.createControlGroupNode();
      this.createControlNode();
      this.createToggleNode();
    },
    createControlGroupNode: function() {
      this.controlGroupNode = put(this.domNode.parentNode, ".dropdown-widget div.input-group");
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'input[type=text][autocomplete=off][readonly].form-control');
      if (this.domNode.getAttribute("placeholder")) {
        put(this.controlNode, '[placeholder='+this.domNode.getAttribute('placeholder')+']');
      }
    },
    createToggleNode: function() {
      this.toggleNode = put(this.controlGroupNode, 'span.input-group-btn button[type=button].btn.btn-default span.caret+span.sr-only $<', 'Toggle Dropdown');
    },
    createDropdownNode: function() {
      this.dropdownNode = put(this.domNode.parentNode, 'div.select-list.hidden');
    },
    open: function() {
      var self = this;
      this.updateStyles();
      domclass.remove(this.dropdownNode, 'hidden');
      this.closeHandler = on(document, 'click', function(e) {
        if (!dom.isDescendant(e.target, self.dropdownNode)) {
          e.preventDefault();
          e.stopPropagation();
          self.close();
        }
      });
    },
    close: function() {
      domclass.add(this.dropdownNode, 'hidden');
      this.closeHandler.remove();
    },
    toggle: function(e) {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      if (domclass.contains(this.dropdownNode, "hidden")) {
        this.open();
      } else {
        this.close();
      }
    },
    addStyles: function() {
      this.domNode.parentNode.style.position = "relative";
      this.dropdownNode.style.position = "absolute";
      this.dropdownNode.style.width = "100%";
      this.dropdownNode.style.zIndex = 10;
    },
    updateStyles: function() {
      var box = geometry.getMarginBox(this.controlNode.parentNode);
      this.dropdownNode.style.top = box.t + box.h + "px";
    },
  });
});
