define(["dojo/_base/declare", "sb/css/Theme", "put-selector/put"], function(declare, Theme, put) {
  var theme = new (declare([Theme], {
    createButtonGroupNode: function() {
      this.buttonGroupNode = this.buttonGroupNode || put(this.domNode, "+div");
    },
    createUploadButton: function() {
      this.uploadButton = put(this.buttonGroupNode, "button.btn.btn-default[role=button][type=button][style=position:relative] span.fa.fa-upload<", " Upload");
    },
    createFileInput: function() {
      this.fileInput = put(this.uploadButton, "input[type=file][tabindex=-1][style=\"position:absolute;top:0;left:0;right:0;bottom:0;opacity:0;width:100%\"]");
      if (this.selection.size != 1) {
        put(this.fileInput, "[multiple]");
      }
    },
    createBrowseButton: function() {
      this.browseButton = put(this.buttonGroupNode, "button.btn.btn-default.ml2[role=button][type=button] span.fa.fa-folder-open<", "Browse");
    },
    createStatusNode: function() {
      this.statusNode = this.statusNode || put(this.buttonGroupNode, "-div");
    },
    createErrorStatus: function(message) {
      return put("p.alert.alert-danger", message);
    },
    createLoadingStatus: function() {
      return put("span.fa.fa-spinner.fa-spin.fa-lg");
    },
    createDisplayNode: function() {
      this.displayNode = put(this.domNode, "-div.mb2");
    },
    renderSelectionItem: function(item) {
      if (item.mime_type.split('/')[0] == "image") {
        put(this.displayNode, "img.mb2[src=$]", item.thumbnail);
      }
      put(this.displayNode, "div.mb2", item.filename);
    },
    createListNode: function() {
      this.listNode = put(this.domNode, "-div.dgrid-autoheight.dbootstrap-grid.mb2[style=display:none]");
    }
  }))();
  return theme;
})