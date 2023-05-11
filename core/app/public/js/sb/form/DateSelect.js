define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "sb/form/_DropdownCalendarMixin"
], function (declare, lang, Widget, Templated, _DropdownCalendarMixin) {
  return declare([Widget, Templated, _DropdownCalendarMixin], {
    closeOnSelect: true,
    buildRendering: function() {
      this.inherited(arguments);
      this.createSelectionNode();
    },
    postCreate: function() {
      this.inherited(arguments);
      this.selection.on("change", lang.hitch(this, "onSelect"));
    },
    createSelectionNode: function() {
      this.selectionNode = this.controlNode;
    },
    onSelect: function () {
      if (this.closeOnSelect) {
        this.close();
        this.controlNode.focus();
      }
    },
    renderSelection: function(items) {
      this.inherited(arguments);
      items = items || this.selection.getData();
      this.selectionNode.value = this.get("displayedValue", items);
    }
  });
});
