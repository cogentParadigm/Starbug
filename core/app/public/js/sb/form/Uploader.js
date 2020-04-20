define([
  "dojo/_base/declare",
  "./_UploadButtonBase",
  "./_CollectionMixin",
  "./_SelectionMixin",
  "put-selector/put"
], function (declare, _UploadButtonBase, _CollectionMixin, _SelectionMixin, put) {
  return declare([_UploadButtonBase, _CollectionMixin, _SelectionMixin], {
    buildRendering: function() {
      this.inherited(arguments);
      this.createDisplayNode();
    },
    createDisplayNode: function() {
      this.uploadButtonTheme.createDisplayNode.apply(this);
    },
    renderSelection: function() {
      this.displayNode.innerHTML = "";
      var items = this.selection.getData();
      for (var i = 0; i < items.length; i++) {
        this.uploadButtonTheme.renderSelectionItem.apply(this, [items[i]]);
      }
    }
  });
});
