define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./Select",
  "put-selector/put",
  "dojo/on",
  "dojo/query",
  "dojo/dom-class",
], function (declare, lang, Select, put, on, query, domclass) {
  return declare([Select], {
    searchable:true,
    _lastSelected:false,
    _selectionMark:false,
    _selectedRows:false,
    closeOnSelect: false,
    postCreate: function() {
      this.inherited(arguments);
      this.list.on('dgrid-select', lang.hitch(this, function(e) {
        this._selectedRows = e.rows;
      }));
    },
    createSelectionParams: function() {
      this.inherited(arguments);
      this.selectionParams.size = this.selectionParams.size || 0;
    },
    createControlNode: function() {
      this.controlNode = put(this.controlGroupNode, 'input[type=text][autocomplete=off].form-control');
      if (this.domNode.getAttribute("placeholder")) {
        this.controlNode.setAttribute('placeholder', this.domNode.getAttribute('placeholder'));
      }
    },
    createInputNode: function() {
      this.inputNode = this.controlNode;
    },
    createSelectionNode: function() {
      this.selectionNode = put(this.controlNode.parentNode, '-div.hidden');
    },
    onInput: function(e) {
      var keyCode = (window.event) ? e.which : e.keyCode;
      if (keyCode == 16 && this._lastSelected && this._selectedRows.length == 1) this._selectionMark = this._lastSelected;
      if (e.ctrlKey || keyCode == 16) return;
      clearTimeout(this.interval);
      var shifted = e.shiftKey;
      var current = this._lastSelected;
      if (false !== current) current = this.list.row(current);
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
        if (!domclass.contains(this.dropdownNode, "hidden")) {
          this.close();
          //Stop propagation to prevent closing a parent modal.
          e.stopPropagation();
          this.toggleNode.focus();
        }
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
        if (this._selectedRows && this._selectedRows.length) {
          for (var r in this._selectedRows) {
            var row = this._selectedRows[r];
            if (typeof row.data != "undefined")  {
              this.selection.get(row.data.id).then(lang.hitch(this, function(selected) {
                if (selected) this.selection.remove(row.data.id);
                else this.selection.add([row.data]);
              }));
            }
          }
        }
        this._selectedRows = this._lastSelected = this._selectionMark = false;
      }
    },
    renderSelection: function() {
      this.selectionNode.innerHTML = '';
      var items = this.selection.getData();
      if (items.length > 0) {
        domclass.remove(this.selectionNode, 'hidden');
      } else {
        domclass.add(this.selectionNode, 'hidden');
      }
      for (var i = 0;i<items.length;i++) {
        var button = put(this.selectionNode, 'button[type=button].btn.btn-primary.btn-xs[style=margin:0 5px 5px 0] $ span.fa.fa-times<', items[i].label + ' ');
        this.attachDeselection(button, items[i].id);
      }
    },
    attachDeselection: function(button, id) {
      on(button, 'click', lang.hitch(this, function() {
        this.selection.remove(id);
      }));
    }
  });
});
