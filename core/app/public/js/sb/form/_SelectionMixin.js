define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "../data/Selection",
  "dojo/on"
], function (declare, lang, Selection, on) {

  // _SelectionMixin
  // ---------------------------------------------------------------------------
  // This mixin adds selection management to a form contol.
  //
  // A selection is simply a value or set of values. This mixin will:
  // - initialize a selection (@see ../../data/Selection.js)
  // - attaches a change listener to the selection which calls refresh
  // - provides a default implementation of refresh which populates this.domNode.value and emits a change event
  // - if you set a collection property, it will use that to look up records by id
  //


 return declare(null, {
    collection: false,
    postMixInProperties: function() {
      this.inherited(arguments);
      this.createSelectionParams();
      this.selection = new Selection(this.selectionParams);
      this.selection.on('change', lang.hitch(this, 'refresh'));
    },
    createSelectionParams: function() {
      this.selectionParams = this.selectionParams || {};
    },
    refresh: function() {
      var ids = [];
      var items = this.selection.getData();
      for (var i = 0;i<items.length;i++) {
        ids.push(this.selection.getIdentity(items[i]));
      }
      this.domNode.value = ids.join(',');
      on.emit(this.domNode, "change", {bubbles: true, cancelable: true});
      this.renderSelection();
      if (typeof this.updateStyles == "function") {
        this.updateStyles();
      }
    },
    startup: function() {
      this.inherited(arguments);
      var self = this;
      if (false !== this.collection && this.domNode.value !== "") {
        this.collection.filter({id:this.domNode.value}).fetch().then(function(results) {
          if (results.length && self.domNode) {
            self.selection.add(results);
          }
        });
      }
    },
    _setValueAttr: function(value) {
      this.selection.selection.setData([]);
      if (value.length) {
        if (false === this.collection) {
          this.selection.add(value);
        } else {
          this.collection.filter({id:value}).fetch().then(lang.hitch(this, function(results) {
            if (results.length) {
              this.selection.add(results);
            } else {
              this.refresh();
            }
          }));
        }
      } else {
        this.refresh();
      }
    },
    _getValueAttr: function() {
      return this.domNode.value;
    },
    renderSelection: function() {
      this.list.renderArray(this.selection.getData());
    }
  });
});
