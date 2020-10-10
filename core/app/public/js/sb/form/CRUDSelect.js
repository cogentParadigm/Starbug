define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./Select",
  "./_CRUDMixin"
], function (declare, lang, Select, _CRUDMixin) {
  return declare([Select, _CRUDMixin]);
});
