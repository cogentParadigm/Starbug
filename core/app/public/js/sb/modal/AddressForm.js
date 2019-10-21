define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./Form",
  "dojo/query"
], function (declare, lang, Form, query) {
  return declare([Form], {
    constructor: function() {
      this.defaultOptions.url = WEBSITE_URL + 'address/';
      this.defaultOptions.locale = 'US';
      this.inherited(arguments);
    },
    updateLocale: function(evt) {
      this.options.locale = evt.target.options[evt.target.selectedIndex].value;
      this.show(this.itemId, {code: this.options.locale});
    },
    loadForm: function(data) {
      this.inherited(arguments);
      query('.country-field', this.form).on('change', lang.hitch(this, 'updateLocale'));
    }
  });
});