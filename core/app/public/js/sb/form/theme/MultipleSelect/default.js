define(["dojo/_base/declare", "sb/css/Theme", "put-selector/put"], function(declare, Theme, put) {
  var theme = new (declare([Theme], {
    createSelectionNode: function() {
      this.selectionNode = put(this.controlNode.parentNode, '-div.hidden');
    },
    createSelectionItem: function(item) {
      return put('button[type=button].btn.btn-primary.btn-xs[style=margin:0 5px 5px 0] $ span.fa.fa-times<', item.label + ' ');
    }
  }))();
  return theme;
})