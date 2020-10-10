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
  "dojo/debounce"
], function (declare, lang, Widget, Templated, _DropdownListMixin, List, ListSelection, put, Memory, on, debounce) {
  return declare(null, {
    filterAttrName:'keywords',
    searchThreshold:0,
    searchable:false,
    autoSubmit:true,
    constructor: function() {
      this.debouncedSearch = debounce(lang.hitch(this, 'search'), 300);
    },
    buildRendering: function() {
      this.inherited(arguments);
      if (this.searchable) {
        this.createInputNode();
        this.focusTargetNode = this.inputNode;
        on(this.inputNode, 'keydown', lang.hitch(this, 'onInput'));
        on(this.inputNode, "paste", lang.hitch(this, "onPaste"));
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
      } else if (this.query.query[this.filterAttrName]) {
        var values = {};
        values[this.filterAttrName] = true;
        this.query.filter(values, true);
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
      } else if (this.autoSubmit && this.isInputCharacter(keyCode)) {
        this.debouncedSearch();
      }
    },
    onPaste: function(e) {
      if (this.autoSubmit) {
        this.debouncedSearch();
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
    },
    isInputCharacter: function(keyCode) {
      return (keyCode == 8)               || // backspace
        (keyCode > 47 && keyCode < 58)    || // number keys
        (keyCode > 64 && keyCode < 91)    || // letter keys
        (keyCode > 95 && keyCode < 112)   || // numpad keys
        (keyCode > 185 && keyCode < 193)  || // ;=,-./` (in order)
        (keyCode > 218 && keyCode < 223);    // [\]' (in order)
    }
  });
});
