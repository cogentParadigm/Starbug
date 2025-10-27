define([
  "dojo/_base/declare",
  "./Number"
], function(declare, NumberWidget){
  return declare([NumberWidget], {
    normalizationPattern: /[^0-9a-fA-F]/g,
    restoreCursor: function() {
      var cursor = this.cursorPosition;
      if (cursor == 1) {
        cursor += 1;
      }
      this.domNode.setSelectionRange(cursor, cursor);
    },
    format: function(value) {
      return '#' + this.inherited(arguments).toUpperCase().substr(0, 6);
    }
  });
});
