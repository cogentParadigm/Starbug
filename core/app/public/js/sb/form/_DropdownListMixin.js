define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./_SelectionMixin",
  "./_DropdownMixin",
  "../list/Dropdown",
  "dgrid/OnDemandList",
  "sb/grid/_StoreMixin",
  "put-selector/put",
  "dojo/on",
  "dojo/dom-class",
  "../data/Query",
  "../data/Selection",
  "sb/store/Api"
], function (declare, lang, _SelectionMixin, _DropdownMixin, List, _VirtualScrolling, _StoreMixin, put, on, domclass, Query, Selection, Api) {
  return declare([_SelectionMixin, _DropdownMixin], {
    model: false,
    collection: false,
    pagingMode: "virtual",
    listClass: false,
    postMixInProperties: function() {
      this.inherited(arguments);
      this.query = this.query || {};
      this.query = new Query({query:this.query});
      if (false == this.collection && false !== this.model) {
        this.collectionParams = this.collectionParams || {};
        this.collectionParams.model = this.collectionParams.model || this.model;
        this.collectionParams.action = this.collectionParams.action || "select";
        this.collection = (new Api(this.collectionParams)).filter(this.query.query);
      }
      this.listParams = this.listParams || {};
      this.listParams.delegate = this.selection;
      this.listParams.deselectOnRefresh = (this.selection.size == 1);
      this.query.on('change', lang.hitch(this, function(e) {
        this.list.set('collection', this.collection.filter(e.query));
      }));
      this.listClass = this.listClass || List;
      if (this.pagingMode == "virtual") {
        this.listClass = declare([this.listClass, _VirtualScrolling]);
      } else if (this.pagingMode == "none") {
        this.listClass = declare([this.listClass, _StoreMixin]);
      }
    },
    buildRendering: function() {
      this.inherited(arguments);
      this.listNode = put(this.dropdownNode, 'div.dgrid-autoheight');
    },
    postCreate:function() {
      this.inherited(arguments);
      this.list = new this.listClass(this.listParams, this.listNode);
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
