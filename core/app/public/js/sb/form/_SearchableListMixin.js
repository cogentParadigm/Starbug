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
  "dojo/query",
  "dojo/dom-class",
], function (declare, lang, Widget, Templated, _DropdownListMixin, List, ListSelection, put, Memory, on, query, domclass) {
  return declare(null, {
    filterAttrName:'keywords',
    searchThreshold:0,
    searchable:false,
    _lastSelected:false,
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
        if (this.isOpened()) {
          var values = {};
          values[this.filterAttrName] = keywords;
          this.query.filter(values);
        } else {
          this.query.query[this.filterAttrName] = keywords;
          this.open();
        }
      } else {
        delete this.query.query[this.filterAttrName];
      }
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
      if (keyCode == 13) { // Enter
        e.preventDefault();
        this.search();
      } else if (keyCode == 40) { // Down Arrow
        e.preventDefault();
        this.focusDropdown();
      }
    },
    refresh: function() {
      clearTimeout(this.interval);
      this.inherited(arguments);
      if (this.list) {
        this.list.clearSelection();
      }
      if (this.searchable && this.inputNode.value.length) {
        delete this.query.query[this.filterAttrName];
        this.inputNode.value = "";
        this.inputNode.focus();
      }
    }
  });
});
