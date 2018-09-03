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
      this.focusTargetNode = this.inputNode = put(this.listNode, "-div.dropdown-search input[type=text][autocomplete=off][placeholder=Search..][tabindex=-1].form-control");
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
      var shifted = e.shiftKey;
      var current = this._lastSelected;
      if (current) current = this.list.row(current);
      this.list.clearSelection();
      var target = false;
      if (keyCode == 38) { //UP
        if (current) target = this.list.up(current);
      } else if (keyCode == 40) { //DOWN
        if (domclass.contains(this.dropdownNode, "hidden")) {
          this.open();
        } else {
          target = current ? this.list.down(current) : query(".dgrid-row", this.list.domNode)[0];
        }
      } else if (keyCode == 27) { //ESC
        this.close();
        //Stop propagation to prevent closing a parent modal.
        e.stopPropagation();
      } else if (keyCode != 9) {
        this.interval = setTimeout(lang.hitch(this, 'search'), 500);
      }
      if (target) {
        if (shifted) {
          this.list.select(this._selectionMark, target);
        } else {
          this.list.select(target);
        }
        if (target.element) this.list.scrollTo({y:(target.element.rowIndex-1)*this.list.rowHeight});
        this._lastSelected = this.list.row(target).id;
      }
      if (keyCode == 13) { //ENTER
        e.preventDefault();
        e.stopPropagation();
        if (current && typeof current.data != "undefined")  {
          this.selection.get(current.data.id).then(lang.hitch(this, function(selected) {
            if (selected) this.selection.remove(current.data.id);
            else this.selection.add([current.data]);
          }));
        }
        this._lastSelected = false;
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
