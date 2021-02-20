define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "../grid/MultipleSelectionList",
  "dgrid/Keyboard",
  "put-selector/put",
  "dojo/on"
], function (declare, lang, List, Keyboard, put, on) {
  return declare([List, Keyboard], {
    rowHeight: 20,
    delegate:false,
    postCreate: function() {
      this.inherited(arguments);
      this.domNode.style.borderBottom = "1px solid #DDD";
      this.bodyNode.style.maxHeight = "160px";
      this.bodyNode.style.overflow = "auto";
      var addSelection = lang.hitch(this, function(event) {
        event.preventDefault();
        event.stopPropagation();
        for (var id in this.selection) {
          if (this.selection[id])  {
            this.delegate.get(id).then(lang.hitch(this, function (selected) {
              if (selected) this.delegate.remove(id);
              else this.delegate.add([this.row(id).data]);
            }));
          }
        }
      });
      // Attach _handleSelect to enter key, it's already
      // attached to space bar in dgrid/Selection
      this.addKeyHandler(13, lang.hitch(this, function (event) {
        this._handleSelect(event, event.target);
      }));
      this.addKeyHandler(32, addSelection); // Space
      this.addKeyHandler(13, addSelection); // Enter
    },
    renderRow: function(object, options) {
      var self = this;
      var label = object.label.length ? {innerHTML: object.label} : {innerHTML: "&nbsp;"};
      var node = put('a.list-group-item.list-group-item-action.inset-focus', label);
      this.delegate.get(object.id).then(function(selected) {
        if (selected) put(node, 'span.pull-right span.fa.fa-check.text-success.green');
        on(node, 'click', function(e) {
          if (!selected) self.delegate.add([object]);
          else if (self.delegate.size != 1) self.delegate.remove(object.id);
        });
      });
      return node;
    }
  });
});
