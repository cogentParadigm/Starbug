define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "./_SelectionListMixin",
  "./_CRUDMixin"
], function (declare, lang, Widget, Templated, _SelectionListMixin, _CRUDMixin) {
  return declare([Widget, Templated, _SelectionListMixin, _CRUDMixin]);
});
