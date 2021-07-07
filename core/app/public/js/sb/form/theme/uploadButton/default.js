define(["dojo/_base/declare", "dojo/_base/lang", "sb/css/Theme", "put-selector/put", "dojo/on"], function(declare, lang, Theme, put, on) {
  var theme = new (declare([Theme], {
    createButtonGroupNode: function() {
      this.buttonGroupNode = this.buttonGroupNode || put(this.domNode, "+div");
    },
    createUploadButton: function() {
      this.uploadButton = put(this.buttonGroupNode, "button.btn.btn-default[role=button][type=button][style=position:relative] span.fa.fa-upload<", " Upload");
    },
    createFileInput: function() {
      this.fileInput = put(this.uploadButton, "input[type=file][tabindex=-1][style=\"position:absolute;top:0;left:0;right:0;bottom:0;opacity:0;width:100%\"]");
      if (this.selection && this.selection.size != 1) {
        put(this.fileInput, "[multiple]");
      }
    },
    createBrowseButton: function() {
      this.browseButton = put(this.buttonGroupNode, "button.btn.btn-default[role=button][type=button][style=margin-left:0.5rem] span.fa.fa-folder-open<", "Browse");
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
      this.displayNode = put(this.domNode, "-div[style=margin-bottom:10px]");
    },
    renderSelectionItem: function(item) {
      if (item.mime_type.split('/')[0] == "image") {
        put(this.displayNode, "img[src=$][style=margin-bottom:10px]", item.thumbnail);
      }
      on(put(this.displayNode, "a.btn.btn-default[href=javascript:;][style=vertical-align:top;margin-left:5px] span.fa.fa-times<"), "click", lang.hitch(this, function() {
        this.selection.remove(item.id);
      }));
      put(this.displayNode, "div[style=margin-bottom:10px]", item.filename);
    },
    createListNode: function() {
      this.listNode = put(this.domNode, "-div.dgrid-autoheight.dbootstrap-grid[style=display:none;margin-bottom:10px]");
    }
  }))();
  return theme;
})