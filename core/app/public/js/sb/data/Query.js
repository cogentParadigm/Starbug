define([
  "dojo/_base/declare",
  "dojo/Evented",
  "dojo/_base/lang",
  "dojo/on",
  "dojo/dom-class",
  "dojo/dom-attr",
  "dojo/query",
  "dijit/registry"
], function (declare, Evented, lang, on, domclass, attr, query, registry) {
  return declare([Evented], {
    scope:'default',
    query:null,
    resetQuery:null,
    resetting:false,
    constructor:function(kwArgs) {
      lang.mixin(this, kwArgs);
      this.query = this.query || {};
      this.resetQuery = this.resetQuery || lang.clone(this.query);
      this.listen();
    },

    listen: function(callback) {
      var self = this;
      //LISTEN TO FILTERS
      query('[data-refresh=' + self.scope + ']').forEach(function(btn) {
          domclass.remove(btn, 'hidden');
        });
      on(window.document, '[data-filter=' + self.scope + ']:change,[data-filter=' + self.scope + ']:input,[data-refresh=' + self.scope + ']:click', function(e) {
        if (self.resetting) return;
        query('[data-reset=' + self.scope + ']').forEach(function(btn) {
          domclass.remove(btn, 'hidden');
        });
        self.applyFilterFromInput(e.target, callback);
      });
      //SAVE VALUES FOR RESETTING
      query('[data-filter=' + self.scope + ']').forEach(function(input) {
        var value = attr.get(input, 'value');
        if (attr.has(input, 'data-save')) {
          var saveScope = attr.get(input, 'data-save');
          var name = attr.get(input, 'name');
          var saveKey = saveScope + '_' + self.scope + '_' + name;
          if (localStorage.getItem(saveKey)) {
            value = localStorage.getItem(saveKey);
            attr.set(input, 'value', value);
            self.query[name] = value;
            self.resetQuery[name] = value;
          }
        }
        attr.set(input, 'data-reset-value', value);
      });
      //ATTACH RESET
      on(window.document, '[data-reset=' + self.scope + ']:click', function(e) {
        self.resetting = true;
        query('[data-filter=' + self.scope + '][data-reset-value]').forEach(function(input) {
          var name = self.getInputName(input);
          var value = attr.get(input, 'data-reset-value');
          self.setInputValue(input, value);
        });
        self.query = lang.clone(self.resetQuery);
        domclass.add(this, 'hidden');
        self.emit('reset', {query:self.query});
        self.resetting = false;
      });
    },

    applyFilterFromInput: function(node) {
      var name = this.getInputName(node);
      var value = this.getInputValue(node);
      if (attr.has(node, 'data-save')) {
        var saveScope = attr.get(node, 'data-save');
        var saveKey = saveScope + '_' + this.scope + '_' + name;
        localStorage.setItem(saveKey, value);
      }
      var filters = {};
      if (name.substr(-2) == '[]') {
        filters[name.substr(0, name.length-2)] = [value];
      } else {
        filters[name] = value;
      }
      var remove = false;
      if (node.tagName == "INPUT" && node.type == "checkbox" && !node.checked) {
        remove = true;
      }
      this.filter(filters, remove);
    },

    filter: function(params, remove) {
      for (var k in params) {
        if (lang.isArray(params[k]) && lang.isArray(this.query[k])) {
          if (remove) {
            this.query[k] = this.query[k].filter(function(i) {return params[k].indexOf(i) < 0;});
          } else {
            this.query[k] = this.query[k].concat(params[k]);
          }
        } else {
          if (remove) {
            delete this.query[k];
          } else {
            this.query[k] = params[k];
          }
        }
      }
      this.emit('change', {query:this.query, update:params});
    },

    getInputName: function(node) {
      var name = (typeof node.get == "undefined") ? attr.get(node, 'name') : node.get('name');
      if (!name) name = (typeof node.get == "undefined") ? attr.get(node, 'data-filter-name') : node.get('data-filter-name');
      return name;
    },

    getInputValue: function(node) {
      var widget = registry.byNode(node);
      if (typeof widget == "undefined" || typeof widget.get == "undefined") {
        return (typeof node.get == "undefined") ? attr.get(node, 'value') : node.get('value');
      } else {
        return widget.get('value');
      }
    },

    setInputValue: function(input, value) {
      var widget = registry.byNode(input);
      if (typeof widget == "undefined" || typeof widget.get == "undefined") {
        attr.set(input, 'value', value);
      } else {
        widget.set('value', value);
      }
    }
  });
});
