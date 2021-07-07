define([
  "dojo/_base/declare",
  "dojo/_base/config",
  "dojo/_base/lang",
  "dojo/_base/array",
  "put-selector/put",
  "dojo/on",
  "dojo/request",
  "dojo/has",
  "sb/form/theme/uploadButton/default"
], function (declare, config, lang, array, put, on, request, has, theme) {
  return declare(null, {
    model: "files",
    uploadOnSelect: true,
    browseEnabled: false,
    category:null,
    location: "default",
    url: config.websiteUrl + "upload",
    uploadButtonTheme: theme,
    buildRendering: function() {
      this.inherited(arguments);
      //this.domNode should be a text input with name and value set appropriately
      this.domNode.type = "hidden";
      this.createButtonGroup();
      this.createStatusNode();
    },
    postCreate: function() {
      this.inherited(arguments);
      on(this.fileInput, "change", lang.hitch(this, function() {
        this._files = this.fileInput.files;
        this.onChange(this.getFileList());
      }));
      on(this.uploadButton, "keypress", lang.hitch(this, "onKeyPress"));
      if (this.browseEnabled) {
        on(this.browseButton, "click", lang.hitch(this, "browse"));
      }
    },
    createButtonGroup: function() {
      this.createButtonGroupNode();
      this.createUploadButton();
      this.createFileInput();
      if (this.browseEnabled) {
        this.createBrowseButton();
      }
    },
    createButtonGroupNode: function() {
      this.uploadButtonTheme.createButtonGroupNode.apply(this);
    },
    createUploadButton: function() {
      this.uploadButtonTheme.createUploadButton.apply(this);
    },
    createFileInput: function() {
      this.uploadButtonTheme.createFileInput.apply(this);
    },
    createBrowseButton: function() {
      this.uploadButtonTheme.createBrowseButton.apply(this);
    },
    createStatusNode: function() {
      this.uploadButtonTheme.createStatusNode.apply(this);
    },
    upload: function(formData) {
      formData = formData || {};
      formData.uploadType = this.uploadType;
      if (this.category != null) formData.category = this.category;
      formData.location = this.location;
      formData.oid = config.csrfToken;
      this.onBegin(this.getFileList());
      this.uploadWithFormData(formData);
    },
    browse:function(){
      window.SetUrl = lang.hitch(this, function(url, object) {
        if (this.selection != undefined) {
          this.selection.add([object]);
        }
      });
      window.open(WEBSITE_URL + 'admin/media?modal=true','media','modal,width=1020,height=600');
    },
    reset: function() {
      delete this._files;
      this.fileInput.value = null;
      this.set("status");
    },
    _setStatusAttr: function(value) {
      this.statusNode.innerHTML = '';
      if (value == 'loading') {
        put(this.statusNode, this.uploadButtonTheme.createLoadingStatus.apply(this));
      } else if (value instanceof Element) {
        put(this.statusNode, value);
      } else if (value instanceof String && value.length) {
        this.statusNode.innerHTML = value;
      }
    },
    onKeyPress: function(event) {
      if (event.keyCode == 32 || event.keyCode == 13) { // Space or Enter
        this.fileInput.click();
      }
    },
    onChange: function(data) {
      if (this.uploadOnSelect) {
        this.upload(data[0]);
      }
    },
    onBegin: function(dataArray){
      // summary:
      //    Fires when upload begins
      this.set("status", "loading");
    },
    onProgress: function(ustomEvent){
      // summary:
      //    Stub to connect
      //    Fires on upload progress.
      // customEvent:
      //    - bytesLoaded: Number:
      //      Amount of bytes uploaded so far of entire payload (all files)
      //    - bytesTotal: Number:
      //      Amount of bytes of entire payload (all files)
    //      - type: String:
      //      Type of event (progress or load)
      //    - timeStamp: Number:
      //      Timestamp of when event occurred
    },
    onComplete: function(files){
      // summary:
      //    stub to connect
      //    Fires when all files have uploaded
      //    Event is an array of all files
      this.reset();
      if (files.length && typeof files[0].ERROR != "undefined") {
        this.set("status", this.uploadButtonTheme.createErrorStatus.apply(this, ["The selected file could not be uploaded."]));
        return;
      }
      if (this.selection != undefined) {
        this.selection.add(files);
      }
    },
    onCancel: function(){
      // summary:
      //    Stub to connect
      //    Fires when dialog box has been closed
      //    without a file selection
      this.set("status");
    },
    onAbort: function(){
      // summary:
      //    Stub to connect
      //    Fires when upload in progress was canceled
      this.set("status");
    },
    onError: function(error){
      // summary:
      //    Fires on errors
      console.log(error);
    },
    getFileList: function() {
      var fileArray = [];
      array.forEach(this._files, function(f, i){
        fileArray.push({
          index:i,
          name:f.name,
          size:f.size,
          type:f.type
        });
      }, this);
      return fileArray;
    },
    uploadWithFormData: function(data){
      var fd = new FormData(), fieldName = "uploadedfiles[]";
      array.forEach(this._files, function(f, i){
        fd.append(fieldName, f);
      }, this);

      if(data){
        data.uploadType = this.uploadType;
        for(var nm in data){
          fd.append(nm, data[nm]);
        }
      }

      var self = this;
      var deferred = request(
        this.url,
        {
          method: "POST",
          data: fd,
          handleAs: "json",
          uploadProgress: true,
          headers: {
            Accept: "application/json"
          }
        },
        true
      );

      deferred.promise.response
        .otherwise(function (error){
          console.error(error);
          console.error(error.response.text);
          self.onError(error);
        })
      ;

      function onProgressHandler(event){
        self._xhrProgress(event);

        if(event.type !== "load"){
          return;
        }

        self.onComplete(deferred.response.data);

        // Disconnect event handlers when done
        deferred.response.xhr.removeEventListener("load", onProgressHandler, false);
        deferred.response.xhr.upload.removeEventListener("progress", onProgressHandler, false);

        deferred = null;
      }

      if(has("native-xhr2")){
        // Use addEventListener directly to pass the raw events to Uploader#_xhrProgress
        deferred.response.xhr.addEventListener("load", onProgressHandler, false);
        deferred.response.xhr.upload.addEventListener("progress", onProgressHandler, false);
      }else{
        // If the browser doesn't have upload events, notify when the upload is complete
        deferred.promise.then(function(data){
          self.onComplete(data);
        });
      }
    },

    _xhrProgress: function(evt){
      if(evt.lengthComputable){
        var o = {
          bytesLoaded:evt.loaded,
          bytesTotal:evt.total,
          type:evt.type,
          timeStamp:evt.timeStamp
        };
        if(evt.type == "load"){
          // 100%
          o.percent = "100%";
          o.decimal = 1;
        }else{
          o.decimal = evt.loaded / evt.total;
          o.percent = Math.ceil((evt.loaded / evt.total)*100)+"%";
        }
        this.onProgress(o);
      }
    }
  });
});
