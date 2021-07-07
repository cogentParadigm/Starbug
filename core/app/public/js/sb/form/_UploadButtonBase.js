define([
  "dojo/_base/declare",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "./_CollectionMixin",
  "./_SelectionMixin",
  "./_UploadButtonMixin"
], function (declare, Widget, Templated, _CollectionMixin, _SelectionMixin, _UploadButtonMixin) {
  return declare([Widget, Templated, _CollectionMixin, _SelectionMixin, _UploadButtonMixin]);
});
