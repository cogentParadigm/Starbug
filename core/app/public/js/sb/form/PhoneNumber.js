define([
  "dojo/_base/declare",
  "./Number"
], function(declare, NumberWidget){
  return declare([NumberWidget], {
    maxLength: 10,
    restoreCursor: function() {
      var cursor = this.cursorPosition;
      if (cursor == 1) cursor += 1;
      else if (cursor == 5) cursor += 2;
      else if (cursor == 10) cursor += 1;
      this.domNode.setSelectionRange(cursor, cursor);
    },
    format: function(value) {
      var formatted = [];

      if (value.length) { // Open '(' and first 3 numbers.
        formatted.push('(');
        formatted.push(value.substr(0, 3));
        value = value.slice(3);
      }

      if (value.length) { // Closing ')' and middle 3 numbers.
        formatted.push(') ');
        formatted.push(value.substr(0, 3));
        value = value.slice(3);
      }

      if (value.length) { // Last block of remaining digits.
        formatted.push('-' + value);
      }

      return formatted.join('');
    }
  });
});
