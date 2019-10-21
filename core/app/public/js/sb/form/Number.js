define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dojo/on",
  "dijit/_WidgetBase"
], function(declare, lang, on, Widget){
  return declare([Widget], {
    maxLength: 0,
    postCreate: function() {
      this.domNode.removeAttribute('maxlength');
      on(this.domNode, 'input', lang.hitch(this, 'execute'));
    },
    startup: function() {
      this.execute();
    },
    saveCursor: function() {
      this.cursorPosition = this.domNode.selectionStart;
    },
    restoreCursor: function(original, normalized, formatted) {
      var difference = formatted.length - original.length;
      var cursorPosition = this.cursorPosition + difference;
      this.domNode.setSelectionRange(cursorPosition, cursorPosition);
    },
    execute: function() {
      if(this.domNode.value) {
        this.saveCursor();
        var original = this.domNode.value;
        var value = this.normalize(this.domNode.value);
        this.domNode.value = this.format(value);
        this.restoreCursor(original, value, this.domNode.value);
      }
    },
    normalize: function(value) {
      value = value.replace(/[^0-9]/g, '');
      if (this.maxLength > 0) {
        value = value.substr(0, this.maxLength);
      }
      return value;
    },
    format: function(value) {
      return value;
    }
  });
});
