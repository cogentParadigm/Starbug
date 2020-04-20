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
  "sb/form/theme/dropdown/default"
], function (declare, lang, List, Selection, put, Memory, on, dom, domclass, geometry, theme) {
  return declare(null, {
    dropdownTheme: theme,
    buildRendering: function() {
      this.inherited(arguments);
      //this.domNode should be a text input with name and value set appropriately
      this.domNode.type = "hidden";
      this.createControlGroup();
      this.createDropdownNode();
    },
    postCreate:function() {
      this.inherited(arguments);
      this.signals.push(on(document.body, 'focusin', lang.hitch(this, 'onFocus')));
      this.signals.push(on(document.body, 'click', lang.hitch(this, 'onClick')));
      this.signals.push(on(this.domNode.parentNode, "keydown", lang.hitch(this, "onKeydown")));
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
      this.dropdownTheme.createControlGroupNode.apply(this);
    },
    createControlNode: function() {
      this.dropdownTheme.createControlNode.apply(this);
    },
    createFocusNode: function() {
      this.focusTargetNode = this.controlNode;
    },
    createToggleNode: function() {
      this.dropdownTheme.createToggleNode.apply(this);
    },
    createDropdownNode: function() {
      this.dropdownTheme.createDropdownNode.apply(this);
    },
    open: function() {
      this.updateStyles();
      this.focusTargetNode.focus();
      domclass.remove(this.dropdownNode, 'hidden');
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
    isOpened: function() {
      return !this.isClosed();
    },
    isClosed: function() {
      return domclass.contains(this.dropdownNode, "hidden");
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
    },
    onClick: function(e) {
      if (e.target == this.controlNode || this.controlNode.contains(e.target)) {
        if (this.isOpened()) {
          this.focusTargetNode.focus();
        } else {
          this.open();
        }
      } else if (this.toggleNode && (e.target == this.toggleNode || this.toggleNode.contains(e.target))) {
        this.toggle();
      } else if (this.isOpened() && !this.domNode.parentNode.contains(e.target)) {
        this.close();
      }
    },
    onFocus: function(e) {
      if (e.target == this.controlNode || this.controlNode.contains(e.target)) {
        domclass.add(this.domNode, "focused");
      } else {
        domclass.remove(this.domNode, "focused");
        if (this.isOpened() && !this.domNode.parentNode.contains(e.target)) {
          this.close();
        }
      }
    },
    onKeydown: function(e) {
      var keyCode = (window.event) ? e.which : e.keyCode;
      if (keyCode == 27) { //ESC
        if (this.isOpened()) {
          this.close();
          //Stop propagation to prevent closing a parent modal.
          e.stopPropagation();
          this.controlNode.focus();
        }
      } else if ((keyCode == 40 || keyCode == 32) && document.activeElement == this.controlNode) { // Down Arrow or Space
        e.preventDefault();
        if (this.isClosed()) {
          this.open();
        } else {
          this.focusDropdown();
        }
      }
    },
    focusDropdown: function() {
      if (this.dropdownFocusTarget) {
        this.dropdownFocusTarget.focus();
      }
    }
  });
});
