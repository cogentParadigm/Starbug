define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./_SelectionMixin",
  "./_DropdownMixin",
  "../list/Dropdown",
  "put-selector/put",
  "dojo/on",
  "dojo/dom-class",
  "../data/Query",
  "../data/Selection",
  "sb/store/Api"
], function (declare, lang, _SelectionMixin, _DropdownMixin, List, put, on, domclass, Query, Selection, Api) {
  return declare([_SelectionMixin, _DropdownMixin], {
    model: false,
    collection: false,
    postMixInProperties: function() {
      this.inherited(arguments);
      this.query = this.query || {};
      this.query = new Query({query:this.query});
      if (false == this.collection && false !== this.model) {
        this.collection = new Api({model:this.model, action:"select"});
      }
      this.listParams = this.listParams || {};
      this.listParams.delegate = this.selection;
      this.listParams.deselectOnRefresh = (this.selection.size == 1);
      this.query.on('change', lang.hitch(this, function(e) {
        this.list.set('collection', this.collection.filter(e.query));
      }));
    },
    buildRendering: function() {
      this.inherited(arguments);
      this.listNode = put(this.dropdownNode, 'div.dgrid-autoheight');
    },
    postCreate:function() {
      this.inherited(arguments);
      this.list = new List(this.listParams, this.listNode);
      this.list.startup();
    },
    open: function() {
      this.inherited(arguments);
      this.list.set('collection', this.collection.filter(this.query.query));
    },
    refresh: function(event) {
      this.inherited(arguments);
      if (event && this.list && this.list.collection) {
        if (this.list.collection.track == undefined) {
          this.list.refresh();
        } else {
          var target = (event.selection.length > event.previous.length) ? event.selection : event.previous;
          for (var i in target) {
            this.list.collection.emit("update", {target:target[i]});
          }
        }
      }
    },
    focusDropdown: function() {
      this.inherited(arguments);
      this.list.focus();
    }
  });
});
