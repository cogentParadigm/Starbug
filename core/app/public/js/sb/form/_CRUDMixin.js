define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "put-selector/put",
  "dojo/on",
  "dojo/query",
  "sb/store/Api",
  "starbug/form/Dialog"
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
      this.get_data = this.get_data || {};
      this.post_data = this.post_data || {};
      this.url = this.url || '/admin/' + this.model + '/';
      this.dialog = new this.dialogClass({url:this.url, get_data:this.get_data, post_data:this.post_data, callback:lang.hitch(this, function(data) {
        var object_id = query('input[name="'+this.model+'[id]"]').attr('value')[0];
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
      })});
      this.dialog.startup();
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
