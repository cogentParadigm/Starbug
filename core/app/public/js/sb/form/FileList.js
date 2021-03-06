define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "./_CollectionMixin",
  "./_SelectionListMixin",
  "./_UploadButtonMixin",
  "put-selector/put",
  "dojo/on",
  "sb/grid/Grid",
  "sb/grid/_DnDMixin",
  "starbug/grid/columns/handle",
  "starbug/grid/columns/filesize"
], function (declare, lang, Widget, Templated, _CollectionMixin, _SelectionListMixin, _UploadButtonMixin, put, on, Grid, _DnDMixin) {

  var MemoryGrid = declare([Grid, _DnDMixin]);

  return declare([Widget, Templated, _CollectionMixin, _SelectionListMixin, _UploadButtonMixin], {
    listClass: MemoryGrid,
    postMixInProperties: function() {
      this.listParams = this.listParams || {};
      this.listParams.editor = this.listParams.editor || this;
      this.columns = this.columns || [
        starbug.grid.columns.handle({field: "id", label: "-", className: "field-drag"}),
        {
          field: "filename",
          label: "File",
          renderCell: function (object, value, cell) {
            var div = put(cell, "div");
            var full_path = object.url;
            if (object.mime_type.split('/')[0] == "image") {
              var img = put(div, 'img.img-responsive');
              img.src = full_path;
            }
            put(div, 'a[href="'+full_path+'"][target="_blank"][style="word-wrap:break-word"]', value);
          }
        },
        starbug.grid.columns.filesize({field: 'size', label: "Size"}),
        {field: "mime_type", label: "Type"},
        {field: 'modified'},
        {
          field: "id",
          label: "Options",
          renderCell: function (object, value, cell) {
            var div = put(cell, 'div.btn-group');
            remove = put(div, 'button.btn.btn-default[title=Remove][role=button]', put('span.fa.fa-times'));
            on(remove, 'click', lang.hitch(this, function() {
              if (confirm('Are you sure you want to remove this item?')) {
                this.grid.editor.selection.remove(object.id);
              }
            }));
          }
        }
      ];
      this.inherited(arguments);
    },
    createListNode: function() {
      this.uploadButtonTheme.createListNode.apply(this);
    }
  });
});
