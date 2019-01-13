define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dojo/_base/array",
  "bootstrap/Modal",
  "dijit/layout/ContentPane",
  "put-selector/put",
  "dojo/request/xhr",
  "dijit/registry",
  "dojo/query",
  "dojo/on",
  "./theme",
], function(declare, lang, array, Modal, ContentPane, put, xhr, registry, query, on, theme) {
  return declare(Modal, {
    "-chains-": {
      constructor: "manual"
    },
    autoRender: true,
    constructor: function(element, options) {
      if (!element) element = put(window.document.body, "div");
      this.domNode = element;
      this.build();
      this.inherited(arguments, [element, options]);
      this.body = new ContentPane({}, this.bodyNode);
      this.options.url = this.options.url || false;
      this.options.urlParams = this.options.urlParams || {};
      on(this.domNode, "hidden.bs.modal", lang.hitch(this, function() {
        this.setContent("");
      }));
    },
    build: function() {
      this.dialog = put(this.domNode, theme.selector("dialog"));
      this.contentNode = put(this.dialog, theme.selector("contentNode"));
      this.headerNode = put(this.contentNode, theme.selector("headerNode"));
      this.bodyNode = put(this.contentNode, theme.selector("bodyNode"));
      this.renderHeader();
    },
    renderHeader: function() {
      put(this.headerNode, theme.selector("closeButton"), theme.text("closeButton"));
      this.titleNode = put(this.headerNode, theme.selector("titleNode"));
    },
    render: function(data, params) {
      data = data || {};
      params = params || {};
      data.title = data.title || this.options.title;
      data.url = data.url || this.options.url;
      if (data.title) {
        this.titleNode.innerHTML = data.title;
      }
      if (data.url) {
        var query = lang.mixin(lang.clone(this.options.urlParams), params);
        xhr(this.buildUrl(data.url), {query:query}).then(lang.hitch(this, 'setContent'));
      } else if (typeof data.body != "undefined") {
        this.setContent(data.body);
      }
    },
    show: function(data, params) {
      put(window.document.body, ".modal-open");
      this.inherited(arguments);
      if (this.autoRender) this.render(data, params);
    },
    hide: function() {
      put(window.document.body, "!modal-open");
      this.inherited(arguments);
    },
    setContent: function(content) {
      array.forEach(registry.findWidgets(this.bodyNode), function(w) {
        w.destroyRecursive();
      });
      this.body.set('content', content);
      this.attach();
    },
    attach: function() {
      query('.cancel', this.bodyNode).attr('onclick', '').on('click', this.hide.bind(this));
    },
    buildUrl: function(url) {
      return url;
    }
  });
});
