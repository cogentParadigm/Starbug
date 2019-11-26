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
    closeOnSelect: true,
    buildRendering: function() {
      this.inherited(arguments);
      this.createSelectionNode();
    },
    postCreate: function() {
      this.inherited(arguments);
      this.selection.on('change', lang.hitch(this, 'onSelect'));
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
    renderSelection: function() {
      this.selectionNode.value = this.get("displayedValue");
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
