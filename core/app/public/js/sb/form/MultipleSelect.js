define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./Select",
  "put-selector/put",
  "dojo/on",
  "dojo/query",
  "dojo/dom-class",
  "sb/form/theme/MultipleSelect/default"
], function (declare, lang, Select, put, on, query, domclass, theme) {
  return declare([Select], {
    searchable:true,
    closeOnSelect: false,
    createSelectionParams: function() {
      this.inherited(arguments);
      this.selectionParams.size = this.selectionParams.size || 0;
    },
    createInputNode: function() {
      this.inputNode = this.controlNode;
    },
    createSelectionNode: function() {
      theme.createSelectionNode.apply(this);
    },
    open: function() {
      this.inherited(arguments);
      put(this.controlNode, "[!readonly]");
    },
    close: function() {
      this.inherited(arguments);
      put(this.controlNode, "[readonly]");
    },
    renderSelection: function(items) {
      this.selectionNode.innerHTML = "";
      items = items || this.selection.getData();
      if (items.length > 0) {
        domclass.remove(this.selectionNode, "hidden");
      } else {
        domclass.add(this.selectionNode, "hidden");
      }
      for (var i = 0;i<items.length;i++) {
        var button = theme.createSelectionItem.apply(this, [items[i]]);
        put(this.selectionNode, button);
        this.attachDeselection(button, items[i].id);
      }
    },
    attachDeselection: function(button, id) {
      on(button, "click", lang.hitch(this, function(event) {
        event.preventDefault();
        event.stopPropagation();
        this.list.delegate.remove(id);
      }));
    }
  });
});
