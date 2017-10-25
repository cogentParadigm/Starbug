define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "./_SelectionMixin",
  "put-selector/put",
  "dojo/on",
  "dojo/query",
  "sb/store/Api",
  "dgrid/Grid",
  "starbug/form/Dialog",
  "starbug/grid/columns/handle",
  "starbug/grid/columns/html",
  "starbug/grid/columns/options"
], function (declare, lang, Widget, Templated, _SelectionMixin, put, on, query, Api, List, Dialog) {

  var defaultColumns = [
    starbug.grid.columns.handle({field: 'id', label: '-', className: 'field-drag'}),
    starbug.grid.columns.html({field: 'label', label: ''}),
    starbug.grid.columns.options({field: 'id', label: 'Options'})
  ];

  return declare([Widget, Templated, _SelectionMixin], {
    model: false,
    collection: false,
    columns: false,
    editing: false,
    postMixInProperties: function() {
      this.inherited(arguments);
      this.newItemLabel = this.newItemLabel || '<span class="fa fa-plus"></span> New';
      this.listClass = this.listClass || List;
      this.dialogClass = this.dialogClass || Dialog;
      if (false == this.collection && false !== this.model) {
        this.collectionParams = this.collectionParams || {};
        this.collectionParams.model = this.collectionParams.model || this.model;
        this.collectionParams.action = this.collectionParams.action || "select";
        this.collection = new Api(this.collectionParams);
      }
      this.listParams = this.listParams || {};
      this.listParams.collection = this.selection.selection;
      this.listParams.editor = this;
      this.listParams.deselectOnRefresh = (this.selection.size == 1);
      this.listParams.columns = this.columns || defaultColumns;
      if (typeof this.listParams.columns == "string") {
        this.listParams.columns = require(this.listParams.columns);
      }
    },
    buildRendering: function() {
      this.inherited(arguments);
      //this.domNode should be a text input with name and value set appropriately
      this.domNode.type = "hidden";
      this.listNode = put(this.domNode.parentNode, "div.dgrid-autoheight[style=display:none]");
      this.actionsNode = put(this.domNode.parentNode, "div[style=margin-top:15px]");
      this.addButton = put(this.actionsNode, "button.btn.btn-default[type=button]", {innerHTML:this.newItemLabel});
    },
    createSelectionParams: function() {
      this.inherited(arguments);
      this.selectionParams.size = this.selectionParams.size || 0;
      //if (typeof this.selectionParams.valuePrefix == "undefined") this.selectionParams.valuePrefix = '#';
    },
    renderSelection: function() {
      if (this.selection.getData().length > 0) {
        this.listNode.style.display = 'block';
      } else {
        this.listNode.style.display = 'none';
      }
      this.inherited(arguments);
      window.dispatchEvent(new Event("resize"));
    },
    postCreate:function() {
      this.inherited(arguments);
      var self = this;
      this.get_data = this.get_data || {};
      this.post_data = this.post_data || {};
      this.url = this.url || '/admin/' + this.model + '/';
      this.list = new this.listClass(this.listParams, this.listNode);
      this.dialog = new this.dialogClass({url:this.url, get_data:this.get_data, post_data:this.post_data, callback:function(data) {
        var object_id = query('input[name="'+self.model+'[id]"]').attr('value')[0];
        if (false !== self.editing) {
          //allow replacement of an edited object
          if (object_id != self.editing) {
            self.store.remove(self.editing);
          }
          self.editing = false;
        }
        self.collection.filter({'id':object_id}).fetch().then(function(data) {
          self.selection.add(data);
        });
      }});
      this.list.startup();
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
