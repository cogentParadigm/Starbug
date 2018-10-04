define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "./_DropdownListMixin",
  "dgrid/OnDemandList",
  "dgrid/Selection",
  "put-selector/put",
  "dstore/Memory",
  "dojo/on",
  "dojo/dom-class",
], function (declare, lang, Widget, Templated, _DropdownListMixin, List, ListSelection, put, Memory, on, domclass) {
  return declare(null, {
    filterAttrName:'keywords',
    searchThreshold:0,
    searchable:false,
    buildRendering: function() {
      this.inherited(arguments);
      if (this.searchable) {
        this.createInputNode();
        on(this.inputNode, 'keydown', lang.hitch(this, 'onInput'));
      }
    },
    createInputNode: function() {
      this.inputNode = put(this.listNode, "-div.dropdown-search input[type=text][autocomplete=off][placeholder=Search..].form-control");
    },
    search: function(e) {
      var keywords = this.inputNode.value.replace(',','');
      if(keywords.length >= this.searchThreshold) {
        this.query.query[this.filterAttrName] = keywords;
        domclass.remove(this.dropdownNode, 'hidden');
      } else {
        this.query.query[this.filterAttrName] = null;
        domclass.add(self.dropdownNode,'hidden');
      }
      this.list.set('collection', this.collection.filter(this.query.query));
    },
    close: function() {
      clearTimeout(this.interval);
      this.inherited(arguments);
      if (this.searchable) {
        delete this.query.query[this.filterAttrName];
        this.inputNode.value = "";
      }
    },
    onInput: function(e) {
      var keyCode = (window.event) ? e.which : e.keyCode;
      if (e.ctrlKey || keyCode == 16) return;
      clearTimeout(self.interval);
      if (keyCode == 27) { //ESC
        this.close();
      } else {
        this.interval = setTimeout(lang.hitch(this, 'search'), 500);
      }
    },
    refresh: function() {
      clearTimeout(this.interval);
      this.inherited(arguments);
      this.list.clearSelection();
      if (this.searchable && this.inputNode.value.length) {
        delete this.query.query[this.filterAttrName];
        this.inputNode.value = "";
        this.inputNode.focus();
      }
    }
  });
});
