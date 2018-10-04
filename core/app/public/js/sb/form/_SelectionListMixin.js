define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./_SelectionMixin",
  "put-selector/put",
  "dgrid/Grid",
  "starbug/grid/columns/handle",
  "starbug/grid/columns/html",
  "starbug/grid/columns/options"
], function (declare, lang, _SelectionMixin, put, List) {

  var defaultColumns = [
    starbug.grid.columns.handle({field: 'id', label: '-', className: 'field-drag'}),
    starbug.grid.columns.html({field: 'label', label: ''}),
    starbug.grid.columns.options({field: 'id', label: 'Options'})
  ];

  return declare([_SelectionMixin], {
    columns: false,
    postMixInProperties: function() {
      this.inherited(arguments);
      this.listClass = this.listClass || List;
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
      this.list = new this.listClass(this.listParams, this.listNode);
      this.list.startup();
    }
  });
});
