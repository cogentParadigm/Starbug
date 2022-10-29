define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "sb/data/Query",
  "sb/store/Api"
], function(declare, lang, Query, Api) {
  return declare(null, {
    model: false,
    action: false,
    query:{},
    postMixInProperties: function() {
      this.inherited(arguments);
      this.query = this.query || {};
      this.query = new Query({scope: this.model, saveScope: this.model + "Query", query: this.query});
      this.query.on("change", lang.hitch(this, "onQueryChange"));
      this.query.on("reset", lang.hitch(this, "onQueryReset"));
      if (this.model && this.action) {
        this.collection = (new Api({model:this.model, action:this.action})).filter(this.query.query);
      }
    },
    onQueryChange: function(e) {
      this.set("collection", this.collection.root.filter(e.query));
    },
    onQueryReset: function() {
      // override/extend to implement query reset behaviors.
    }
  });
});