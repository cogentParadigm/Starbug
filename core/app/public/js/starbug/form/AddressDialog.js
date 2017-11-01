define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "starbug/form/Dialog",
  "dojo/query"
], function (declare, lang, Dialog, query) {
  return declare([Dialog], {
    url: WEBSITE_URL + 'address/',
    locale:'US',
    updateLocale: function(evt) {
      this.locale = evt.target.options[evt.target.selectedIndex].value;
      this.show(this.item_id, {code: this.locale});
    },
    loadForm: function(data) {
      this.inherited(arguments);
      query('.country-field', this.form).on('change', lang.hitch(this, 'updateLocale'));
    }
  });
});