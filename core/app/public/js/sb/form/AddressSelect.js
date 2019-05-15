define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./CRUDSelect",
  "sb/modal/AddressForm",
  "put-selector/put"
], function (declare, lang, CRUDSelect, Dialog, put) {
  return declare([CRUDSelect], {
    model: 'address',
    searchable: true,
    newItemLabel: 'Add New Address',
    dialogClass: Dialog,
    postMixInProperties: function() {
      this.dialogParams = this.dialogParams || {};
      this.dialogParams.url = this.dialogParams.url || WEBSITE_URL + this.model + '/';
      this.dialogParams.title = this.dialogParams.title || "New Address";
      this.inherited(arguments);
    },
    postCreate: function() {
      this.inherited(arguments);
      this.list.bodyNode.style.maxHeight = "300px";
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'button.form-control.text-left.tl[style=height:114px]');
    },
    createToggleNode: function() {
      this.inherited(arguments);
      put(this.toggleNode, '[style=height:114px]');
    },
    renderSelection: function() {
      this.selectionNode.innerHTML = this.get('displayedValue');
      this.list.refresh();
    }
  });
});
