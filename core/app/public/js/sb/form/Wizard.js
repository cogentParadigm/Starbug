define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dojo/request/xhr",
  "dojo/on",
  "dojo/query",
  "dijit/layout/ContentPane",
  "put-selector/put",
  "dojo/dom-form",
  "dojo/dom-class",
  "dojo/dom-attr"
], function(declare, lang, xhr, on, query, ContentPane, put, domForm, domClass, attr) {
  return declare([ContentPane], {
    formNode:null,
    url: 'form',
    hasNext:false,
    hasPrevious:false,
    query: false,
    transition: "fade",
    autoScroll: false,
    isLayoutContainer: false,
    postCreate: function() {
      if (this.transition == "slide") {
        this.transition = ["fadeInRight", "fadeOutLeft", "fadeInLeft", "fadeOutRight"];
      } else if (this.transition == "fade") {
        this.transition = ["fadeIn", "fadeOut"];
      }
      if (this.transition.length == 2) {
        this.transition = this.transition.concat(this.transition);
      }
      this.loadForm();
    },
    resetAnimation: function() {
      domClass.remove(this.formNode, this.transition.join(" "));
    },
    nextSlideIn: function() {
      domClass.add(this.formNode, this.transition[0]);
      if (this.autoScroll) window.scrollTo(0, 0);
    },
    nextSlideOut: function() {
      this.resetAnimation();
      domClass.add(this.formNode, this.transition[1]);
    },
    backSlideIn: function() {
      domClass.add(this.formNode, this.transition[2]);
      if (this.autoScroll) window.scrollTo(0, 0);
    },
    backSlideOut: function() {
      this.resetAnimation();
      domClass.add(this.formNode, this.transition[3]);
    },
    getValues: function(values) {
      values = values || {};
      values = lang.mixin(domForm.toObject(this.formNode), values);
      return values;
    },
    submit: function(values) {
      put(this.formNode, '.loading');
      values = this.getValues(values);
      return this.show(values);
    },
    show: function(values) {
      this.inherited(arguments);
      values = values || this.query;
      var options = {};
      options.method = this.formNode ? this.formNode.getAttribute("method") : "get";
      if (values) {
        if (options.method == "get") {
          options.query = values;
        } else {
          options.data = values;
        }
      }
      return xhr(this.url, options).then(lang.hitch(this, 'loadForm'));
    },
    next: function(evt) {
      this.nextSlideOut();
      this._onSubmit(evt).then(lang.hitch(this, 'nextSlideIn'));
    },
    back: function(evt) {
      this.backSlideOut();
      this._onSubmit(evt).then(lang.hitch(this, 'backSlideIn'));
    },
    loadForm: function(data) {
      if (data) this.set('content', data);
      var form = query('form', this.domNode);
      this.formNode = form[0];
      var focus = query("[autofocus]");
      if (focus.length) {
        focus[0].focus();
      }
      var back = query('[data-submit=previous]', this.domNode);
      var next = query('[data-submit=next]', this.domNode);
      var finish = query('[data-submit=finish]', this.domNode);
      var change = query('[data-submit=change]', this.domNode);
      this.hasPrevious = (back.length > 0);
      this.hasNext = (next.length > 0);
      if (!finish.length) {
        form.on('submit', this.noopEvent);
      }
      back.on('click', lang.hitch(this, 'back'));
      next.on('click', lang.hitch(this, 'next'));
      change.on('change', lang.hitch(this, '_onSubmit'));
      query('[data-dojo-attach-point]', this.domNode).forEach(lang.hitch(this, function(target) {
        this[target.getAttribute('data-dojo-attach-point')] = target;
      }));
    },
    _onSubmit: function(evt) {
      var values = {};
      if (evt) {
        evt.preventDefault();
        values[evt.currentTarget.getAttribute('name')] = attr.get(evt.currentTarget, 'value');
      }
      return this.submit(values);
    },
    noopEvent: function(evt) {
      evt.preventDefault();
      return false;
    }
  });
});
