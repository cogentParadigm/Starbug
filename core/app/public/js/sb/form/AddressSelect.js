define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./CRUDSelect",
  "starbug/form/AddressDialog",
  "put-selector/put"
], function (declare, lang, CRUDSelect, Dialog, put) {
  return declare([CRUDSelect], {
    model: 'address',
    searchable: true,
    newItemLabel: '<span class="fa fa-plus"></span> Add New Address',
    url: '/address/',
    dialogClass: Dialog,
    postCreate: function() {
      this.inherited(arguments);
      this.list.bodyNode.style.maxHeight = "300px";
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'button.form-control.text-left[style=height:90px]');
    },
    createToggleNode: function() {
      this.inherited(arguments);
      put(this.toggleNode, '[style=height:90px]');
    },
    renderSelection: function() {
      this.selectionNode.innerHTML = this.get('displayedValue');
      this.list.refresh();
    }
  });
});
