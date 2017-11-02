define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dojo/Evented",
  "put-selector/put",
  "dstore/Memory",
  "dojo/on",
  "dojo/query",
  "dojo/dom-class",
  "dojo/dom-geometry"
], function (declare, lang, Evented, put, Memory, on, query, domclass, geometry) {
  return declare([Evented], {
    size:1,
    valuePrefix:'',
    constructor: function(kwArgs) {
      lang.mixin(this, kwArgs);
      this.selection = this.selection || new Memory({data: []});
    },
    get: function(id) {
      return this.selection.get(id);
    },
    getData: function() {
      return this.selection.data;
    },
    getIdentity: function(item) {
      return this.valuePrefix + this.selection.getIdentity(item);
    },
    add:function(items) {
      var target_size = this.selection.data.length + items.length;
      if (this.size === 0) {
        //unlimited
      } else if (this.size == 1) {
        this.selection.setData([]);
      } else if (target_size > this.size) {
        alert("You have reached the limit.");
        return false;
      }
      for (var i = 0;i < items.length;i++) {
        this.selection.put(items[i]);
      }
      this.emit('change', {selection:this.selection.data});
    },
    remove: function(id) {
      this.selection.remove(id);
      this.emit('change', {selection:this.selection.data});
    },
    reset: function() {
      this.selection.setData([]);
      this.emit('change', {selection:this.selection.data});
    }
  });
});
