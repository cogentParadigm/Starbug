define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./_SelectionMixin",
  "sb/list/Dropdown",
  "dgrid/OnDemandList",
  "sb/grid/_StoreMixin",
  "put-selector/put",
  "../data/Query",
  "sb/store/Api",
  "dojo/debounce"
], function (declare, lang, _SelectionMixin, List, _VirtualScrolling, _StoreMixin, put, Query, Api, debounce) {
  return declare(null, {
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
      this.listParams.collection = this.collection;
      this.query.on('change', debounce(lang.hitch(this, function(e) {
        var collection = this.collection.root || this.collection;
        this.collection = collection.filter(e.query);
        this.list.set('collection', this.collection);
        this.set("value", this.get("value"), true);
      }), 100));
      this.listClass = this.listClass || List;
      if (this.pagingMode == "virtual") {
        this.listClass = declare([this.listClass, _VirtualScrolling]);
      } else if (this.pagingMode == "none") {
        this.listClass = declare([this.listClass, _StoreMixin]);
      }
    },
    buildRendering: function() {
      this.inherited(arguments);
      this.domNode.type = "hidden";
      this.createListNode();
    },
    createListNode: function() {
      this.listNode = put(this.domNode, "+div.dgrid-autoheight");
    },
    postCreate:function() {
      this.inherited(arguments);
      this.list = new this.listClass(this.listParams, this.listNode);
      this.list.startup();
    },
    focus: function() {
      this.listNode.focus();
    },
    refresh: function(event) {
      this.inherited(arguments);
      if (event && this.list && this.list.collection) {
        const trackable = this.list.shouldTrackCollection && this.list.collection.track != undefined;
        const targetable = !(event.selection == undefined && event.previous == undefined);
        if (this.updateAllOnRefresh) {
          let results = this.list.collection.results || this.list.collection.fetch();
          results.forEach((item) => {
            this.list.collection.emit("update", {target: item});
          });
        } else if (trackable && targetable) {
          var target = (event.selection.length > event.previous.length) ? event.selection : event.previous;
          for (var i in target) {
            this.list.collection.emit("update", {target:target[i]});
          }
        } else {
          this.list.refresh()
        }
      }
    },
  });
});
