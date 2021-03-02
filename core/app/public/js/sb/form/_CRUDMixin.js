define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "put-selector/put",
  "dojo/on",
  "dojo/query",
  "sb/store/Api",
  "sb/modal/Form"
], function (declare, lang, put, on, query, Api, Dialog) {
  return declare(null, {
    model: false,
    collection: false,
    editing: false,
    newItemIcon: '<span class="fa fa-plus"></span>',
    newItemLabel: 'New',
    postMixInProperties: function() {
      this.inherited(arguments);
      this.dialogClass = this.dialogClass || Dialog;
      this.dialogParams = this.dialogParams || {};
      this.dialogParams.url = this.dialogParams.url || WEBSITE_URL + 'admin/' + this.model.replace(/_/g, "-") + '/';
      if (false == this.collection && false !== this.model) {
        this.collectionParams = this.collectionParams || {};
        this.collectionParams.model = this.collectionParams.model || this.model;
        this.collectionParams.action = this.collectionParams.action || "select";
        this.collection = new Api(this.collectionParams);
      }
    },
    buildRendering: function() {
      this.inherited(arguments);
      this.createActionsNode();
    },
    createActionsNode: function() {
      this.actionsNode = put(this.domNode.parentNode, "div[style=margin-top:15px]");
      this.addButton = put(this.actionsNode, "button.btn.btn-default[type=button]", {innerHTML:this.newItemIcon + ' ' + this.newItemLabel});
    },
    postCreate:function() {
      this.inherited(arguments);
      this.dialogParams.callback = lang.hitch(this, function(data) {
        var object_id = query('input[name="id"]').attr('value')[0];
        if (false !== this.editing) {
          //allow replacement of an edited object
          if (object_id != this.editing) {
            this.collection.remove(this.editing);
          }
          this.editing = false;
        }
        this.collection.filter({'id':object_id}).fetch().then(lang.hitch(this, function(data) {
          this.selection.add(data);
        }));
      });
      this.dialog = new this.dialogClass(this.dialogParams);
      on(this.addButton, 'click', lang.hitch(this, function() {
        this.dialog.show();
      }));
    },
    edit: function(id) {
      this.editing = id;
      this.dialog.show(id);
    },
    copy:function(id) {
      this.dialog.show(false, {copy:id});
    },
    remove: function(id) {
      this.selection.remove(id);
    }
  });
});
