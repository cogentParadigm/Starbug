define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./Dialog",
  "put-selector/put",
  "dojo/query",
  "dojo/on",
  "dojo/dom-class",
  "dojo/request/xhr",
  "dojo/dom-form"
], function(declare, lang, Dialog, put, query, on, domClass, xhr, domForm) {
  return declare(Dialog, {
    itemId: 0,
    constructor: function() {
      this.defaultOptions.callback = false;
      this.defaultOptions.crudSuffixes = true;
      this.defaultOptions.format = "xhr";
      this.defaultOptions.formData = {};
      this.inherited(arguments);
    },
    _onSubmit: function(evt){
      evt.preventDefault();
      var name = evt.target.getAttribute("name");
      if (name) {
        this.options.formData[name] = evt.target.getAttribute("value");
      }
      this.execute();
      return false;
    },
    execute: function() {
      put(this.form, ".loading");
      query(".loading", this.form).style("display", "block");
      var data = lang.mixin(domForm.toObject(this.form), this.options.formData);
      query(".rich-text", this.form).forEach(function(node) {
        data[node.name] = window.tinyMCE.get(node.id).getContent();
      });
      xhr(this.buildUrl(this.options.url), {
        method: 'POST',
        query: this.options.urlParams,
        data: data,
        handleAs: 'html'
      }).then(this.load.bind(this));
    },
    load: function(data) {
      this.setContent(data);
      if (domClass.contains(this.form, 'submitted')) {
        if (false !== this.options.callback) {
          this.options.callback(data, this);
        }
        this.hide();
      }
    },
    setValues: function(args) {
      for (var i in args) {
        query('[name="'+i+'"]', this.form).attr('value', args[i]);
      }
    },
    attach: function() {
      this.inherited(arguments);
      this.form = query('form', this.bodyNode)[0];
      if (this.form) {
        on(this.form, 'submit', function(evt) {
          evt.preventDefault();
        });
        query('.submit, [type=\"submit\"]', this.form).attr('onclick', '').on('click', this._onSubmit.bind(this));
        var focus = query('[autofocus]', this.form);
        if (focus.length) {
          setTimeout(function() {
            focus[0].focus();
          }, 100);
        }
      }
    },
    show: function(data, params) {
      data = data || {};
      if (typeof data == "number" || typeof data == "string") {
        data = {id: data};
      }
      if (data.id) this.itemId = data.id;
      else this.itemId = 0;
      this.inherited(arguments);
    },
    buildUrl: function(url) {
      if (this.options.crudSuffixes) {
        url += this.itemId ? "update/" + this.itemId : "create";
      } else if (this.itemId) {
        url += "/" + this.itemId;
      }
      if (this.options.format) {
        url += "." + this.options.format;
      }
      return url;
    }
  });
});
