define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./CRUDList",
  "../grid/SingleSelectionList",
  "starbug/form/AddressDialog",
  "dojo/on",
  "dojo/query",
  "put-selector/put"
], function (declare, lang, CRUDList, List, Dialog, on, query, put) {
  return declare([CRUDList], {
    model: "address",
    newItemLabel: 'Add New Address',
    url: '/address/',
    listClass: declare([List], {
      renderRow: function(object, options){
        var self = this;
        var label = object.label.length ? object.label : "&nbsp;";
        var node = put('div', {innerHTML: label});
        var buttons = put(node, 'div.btn-group');
        var editButton = put(buttons, 'a.btn.btn-default[href="javascript:;"]', {innerHTML: '<span class="fa fa-edit"></span> Edit'});
        var deleteButton = put(buttons, 'a.btn.btn-default[href="javascript:;"]', {innerHTML: '<span class="fa fa-times"></span> Delete'});
        on(editButton, "click", lang.hitch(this, function() {
          this.editor.edit(object.id);
        }));
        on(deleteButton, "click", lang.hitch(this, function() {
          if (confirm('Are you sure you want to delete this address?')) {
            this.editor.remove(object.id);
          }
        }));
        return node;
      }
    }),
    dialogClass: Dialog,
    buildRendering: function() {
      this.inherited(arguments);
      put(this.listNode, '.dgrid-addresses.clearfix');
    }
  });
});
