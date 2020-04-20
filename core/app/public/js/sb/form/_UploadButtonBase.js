define([
  "dojo/_base/declare",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "./_UploadButtonMixin"
], function (declare, Widget, Templated, _UploadButtonMixin) {
  return declare([Widget, Templated, _UploadButtonMixin]);
});
