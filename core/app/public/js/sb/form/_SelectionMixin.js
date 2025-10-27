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
      this.signals = [
        this.selection.on('change', lang.hitch(this, 'refresh'))
      ];
    },
    postCreate: function() {
      this.inherited(arguments);
      this._setValueAttr = this._setValueAttrHandler;
    },
    createSelectionParams: function() {
      this.selectionParams = this.selectionParams || {};
    },
    refresh: function(event) {
      event = event || {};
      event.suppress = event.suppress || false;
      this.renderValues();
      if (!event.suppress) {
        on.emit(this.domNode, "change", {bubbles: true, cancelable: true});
      }
      on.emit(this.domNode, "refresh", {bubbles: true, cancelable: true});
      this.renderSelection();
      if (typeof this.updateStyles == "function") {
        this.updateStyles();
      }
    },
    renderValues: function() {
      var ids = [];
      var items = this.selection.getData();
      for (var i = 0;i<items.length;i++) {
        ids.push(this.selection.getIdentity(items[i]));
      }
      this.domNode.value = ids.join(',');
    },
    startup: function() {
      this.inherited(arguments);
      var self = this;
      if (false !== this.collection && this.domNode.hasAttribute("value")) {
        this.collection.filter({id:this.domNode.value}).fetch().then(function(results) {
          if (results.length && self.domNode) {
            self.selection.add(results, {suppress: true});
          }
        });
      }
    },
    _setValueAttrHandler: function(value, suppress) {
      suppress = suppress || false;
      this.selection.selection.setData([]);
      if (typeof value == "string" || typeof value == "number") {
        if (false === this.collection) {
          this.selection.add(value);
        } else {
          return this.collection.filter({id:value}).fetch().then(lang.hitch(this, function(results) {
            if (results.length) {
              this.selection.add(results, {suppress: suppress});
            } else {
              this.refresh({suppress: suppress});
            }
          }));
        }
      } else {
        this.refresh({suppress: suppress});
      }
    },
    _getValueAttr: function() {
      return this.domNode.value;
    },
    _getDisplayedValueAttr: function(items) {
      var labels = [];
      items = items || this.selection.getData();
      for (var i = 0; i < items.length; i++) {
        labels.push(items[i].label);
      }
      return labels.join(",");
    },
    renderSelection: function(items) {
      items = items || this.selection.getData();
      this.list.refresh();
      this.list.renderArray(items);
    },
    destroy: function() {
      this.inherited(arguments);
      this.signals.forEach(function(signal) {
        signal.remove();
      });
    },
    get: function(name){
      var names = this._getAttrNames(name);
      return this[names.g] ? this[names.g].apply(this, Array.prototype.slice.call(arguments, 1)) : this._get(name);
    }
  });
});
