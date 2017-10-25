define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./CRUDSelect",
  "../grid/SingleSelectionList",
  "starbug/form/AddressDialog",
  "dojo/on",
  "dojo/query",
  "put-selector/put"
], function (declare, lang, CRUDSelect, List, Dialog, on, query, put) {
  return declare([CRUDSelect], {
    model: "address",
    newItemLabel: '<span class="fa fa-plus"></span> Add New Address',
    url: '/address/',
    listClass: declare([List], {
      renderRow: function(object, options){
        var self = this;
        var label = object.label.length ? object.label : "&nbsp;";
        var node = put('div', {innerHTML: label});
        var editButton = put(node, 'a.edit-address[href="javascript:;"]', 'Edit');
        put(node, 'span', ' | ');
        var deleteButton = put(node, 'a.delete-address[href="javascript:;"]', 'Delete');
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
    dialogClass: Dialog
  });
});
