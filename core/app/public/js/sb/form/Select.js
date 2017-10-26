define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "./_DropdownListMixin",
  "./_SearchableListMixin",
  "put-selector/put",
  "dojo/on"
], function (declare, lang, Widget, Templated, _DropdownListMixin, _SearchableListMixin, put, on) {
  return declare([Widget, Templated, _DropdownListMixin, _SearchableListMixin], {
    buildRendering: function() {
      this.inherited(arguments);
      this.createSelectionNode();
    },
    createSelectionNode: function() {
      this.selectionNode = this.controlNode;
    },
    renderSelection: function() {
      this.selectionNode.value = this.get('displayedValue');
      this.list.refresh();
    },
    focus: function() {
      this.open();
    },
    _getDisplayedValueAttr: function() {
      var labels = [];
      var items = this.selection.getData();
      for (var i = 0; i < items.length; i++) {
        labels.push(items[i].label);
      }
      return labels.join(",");
    }
  });
});
