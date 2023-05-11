define([
  "dojo/_base/declare",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "dojo/on",
  "dojo/dom-class",
], function (declare, _WidgetBase, _AttachMixin, on, domClass) {
  return declare([_WidgetBase, _AttachMixin], {
    togleNode: null,
    postCreate: function() {
      this.inherited(arguments);
      var self = this;
      if (typeof this.closeButton != "undefined") {
        on(this.closeButton, "click", function() {
          self.close();
        });
      }
    },
    open: function() {
      domClass.add(this.domNode, "open");
    },
    close: function() {
      domClass.remove(this.domNode, "open");
    },
    toggle: function() {
      if (domClass.contains(this.domNode, "open")) {
        this.close();
        return "close";
      } else {
        this.open();
        return "open";
      }
    }
  });
});