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
      // The toggleNode and controlNode are two separate elements but sematically
      // they behave as one. Consider a select input - it is just a single element.
      // The dropdown indicator (the toggleNode) is not a separate element.

      // The toggleNode is removed from the tabindex so we are only concerned with click events.
      // Putting focus on the controlNode is the one true trigger event
      on(this.toggleNode, 'click', lang.hitch(this.controlNode, 'focus'));
      // The controlNode remains in the tabindex so we attach the focus event.
      // Note that the controlNode will receive focus on mousedown.
      on(this.controlNode, 'focus,click', lang.hitch(this, 'open'));
      // Closing of the dropdown is tied to blurring of the focus target.
      on(this.focusTargetNode, 'blur', lang.hitch(this, function() {
        //Delay 10 milliseconds to allow focus to move first. Otherwise,
        //we might hide the focusTargetNode if it's inside the dropdown.
        setTimeout(lang.hitch(this, 'close'), 10);
      }));

      // The blur event above will cause the dropdown to close before click events
      // inside the dropdown can fire. We can interrupt that blur by preventing
      // the mousedown event on the dropdown.
      on(this.dropdownNode, 'mousedown', lang.hitch(this, function(e) {
        // This condition avoids interrupting mouse events on the actual focus target.
        if (e.target !== this.focusTargetNode) {
          e.preventDefault();
        }
      }));
    },
    startup: function() {
      this.inherited(arguments);
      this.addStyles();
    },
    createControlGroup: function() {
      this.createControlGroupNode();
      this.createControlNode();
      this.createFocusNode();
      this.createToggleNode();
    },
    createControlGroupNode: function() {
      this.controlGroupNode = put(this.domNode.parentNode, ".dropdown-widget div.input-group");
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'button[type=button].text-left.form-control');
      if (this.domNode.getAttribute("placeholder")) {
        put(this.controlNode, {innerHTML: this.domNode.getAttribute('placeholder')});
      }
    },
    createFocusNode: function() {
      this.focusTargetNode = this.controlNode;
    },
    createToggleNode: function() {
      this.toggleNode = put(this.controlGroupNode, 'span.input-group-btn button[type=button][tabindex=-1].btn.btn-default span.fa.fa-caret-down+span.sr-only $<', 'Toggle Dropdown');
    },
    createDropdownNode: function() {
      this.dropdownNode = put(this.domNode.parentNode, 'div.select-list.hidden');
    },
    open: function() {
      var self = this;
      this.updateStyles();
      domclass.remove(this.dropdownNode, 'hidden');
      this.focusTargetNode.focus();
      if (this.focusTargetNode != this.controlNode) {
        this.controlNode.tabIndex = -1;
      }
    },
    close: function() {
      domclass.add(this.dropdownNode, 'hidden');
      if (this.controlNode.tabIndex == -1) {
        this.controlNode.removeAttribute('tabindex');
      }
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
      var box = geometry.position(this.controlGroupNode);
      var parent = geometry.position(this.domNode.parentNode);
      this.dropdownNode.style.top = (box.y - parent.y) + box.h + "px";
    },
    focus: function() {
      this.controlNode.focus();
    }
  });
});
