define([
  "dojo/_base/declare",
  "dojo/_base/lang"
], function (declare) {
  return declare(null, {
    constructor: function(args) {
      this.selectors = args.selectors || {};
      this.content = args.content || {};
    },
    selector: function(component) {
      return (typeof this.selectors[component] == "undefined") ? "" : this.selectors[component];
    },
    text: function(component) {
      return (typeof this.content[component] == "undefined") ? "" : this.content[component];
    }
  });
});