define(["dojo/_base/declare", "sb/css/Theme", "put-selector/put"], function(declare, Theme, put) {
  var theme = new (declare([Theme], {
    createSelectionNode: function() {
      this.selectionNode = put(this.controlNode.parentNode, '-div.hidden.pa2');
    },
    createSelectionItem: function(item) {
      return put('button[type=button].btn.btn-default.ph2.pv1.f8[style=margin:0 5px 5px 0] $ span.material-icons.f8.v-mid $<', item.label + ' ', 'close');
    }
  }))();
  return theme;
})