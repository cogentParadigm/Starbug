define([
  "dojo/_base/declare",
  "sb/store/Api"
], function (declare, Api) {
  return declare(null, {
    model: false,
    collection: false,
    postMixInProperties: function() {
      this.inherited(arguments);
      if (false == this.collection && false !== this.model) {
        this.collectionParams = this.collectionParams || {};
        this.collectionParams.model = this.collectionParams.model || this.model;
        this.collectionParams.action = this.collectionParams.action || "select";
        this.collection = new Api(this.collectionParams);
      }
    }
  });
});
