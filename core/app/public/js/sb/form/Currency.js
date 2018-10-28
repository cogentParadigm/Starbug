define([
  "dojo/_base/declare",
  "./Number"
], function(declare, NumberWidget){
  return declare([NumberWidget], {
    locale: 'en-US',
    currency: 'USD',
    minorUnits: 2,
    postCreate: function() {
      this.inherited(arguments);
      this.price = new Intl.NumberFormat(this.locale, {
        style: 'currency',
        currency: this.currency,
        minimumFractionDigits: this.minorUnits
      });
    },
    format: function(value) {
      return this.price.format(value / Math.pow(10, this.minorUnits));
    }
  });
});
