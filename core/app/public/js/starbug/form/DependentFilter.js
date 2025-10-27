define([
  "dojo/_base/declare",
  "starbug/form/Dependent"
], function(declare, Dependent){
  return declare([Dependent], {
    filterKey: false,
    postCreate: function() {
      this.inherited(arguments);
      this.filterKey = this.filterKey || this.domNode.getAttribute("data-depend");
    },
    toggleDependency: function(dependency) {
      var filters = {};
      filters[this.filterKey] = dependency;
      this.query.filter(filters);
    }
  });
});
