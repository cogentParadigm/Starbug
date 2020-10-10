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
    saveScope: false,
    query:null,
    resetQuery:null,
    constructor:function(kwArgs) {
      lang.mixin(this, kwArgs);
      this.query = this.query || {};
      this.resetQuery = this.resetQuery || lang.clone(this.query);
      this.savedQuery = (this.saveScope && localStorage.getItem(this.saveScope)) ? JSON.parse(localStorage.getItem(this.saveScope)) : {};
      if (this.saveScope) {
        var savedScopes = localStorage.getItem("savedScopes") ? JSON.parse(localStorage.getItem("savedScopes")) : [];
        if (savedScopes.indexOf(this.saveScope) < 0) {
          savedScopes.push(this.saveScope);
          localStorage.setItem("savedScopes", JSON.stringify(savedScopes));
        }
      }
      this.listen();
    },

    listen: function(callback) {
      var self = this;
      //LISTEN TO FILTERS
      query('[data-refresh=' + this.scope + ']').forEach(function(btn) {
          domclass.remove(btn, 'hidden');
        });
      this.changeListener = on.pausable(window.document, '[data-filter=' + this.scope + ']:change,[data-filter=' + this.scope + ']:input,[data-refresh=' + this.scope + ']:click', lang.hitch(this, function(e) {
        if (e.target.tagName == "INPUT" && e.target.getAttribute("type") == "text" && e.target.getAttribute("name") && e.type == "change") {
          //ignore change events from text inputs since the input event will do what we need
          //and the change event will fire undesirably when changing focus
          return;
        }
        if (e.target.tagName == "SELECT" && e.type == "input") {
          //ignore input events from select elements since the change event will do what we need
          //and the special cases that require the input event are not relevant for select elements
          return;
        }
        query('[data-reset=' + this.scope + ']').forEach(function(btn) {
          domclass.remove(btn, 'hidden');
        });
        this.applyFilterFromInput(e.target, callback);
      }));
      //SAVE VALUES FOR RESETTING
      query('[data-filter=' + this.scope + ']').forEach(lang.hitch(this, function(input) {
        var value = this.getInputValue(input);
        attr.set(input, 'data-reset-value', value);
        this.resetQuery[name] = value;
        if (this.saveScope) {
          var name = attr.get(input, 'name');
          if (typeof this.savedQuery[name] !== "undefined") {
            value = this.savedQuery[name];
            this.setInputValue(input, value);
            this.query[name] = value;
          }
        }
      }));
      //ATTACH RESET
      on(window.document, '[data-reset=' + self.scope + ']:click', function(e) {
        self.changeListener.pause();
        query('[data-filter=' + self.scope + '][data-reset-value]').forEach(function(input) {
          var name = self.getInputName(input);
          var value = attr.get(input, 'data-reset-value');
          self.setInputValue(input, value);
        });
        if (this.saveScope) {
          localStorage.removeItem(this.saveScope);
        }
        self.query = lang.clone(self.resetQuery);
        domclass.add(this, 'hidden');
        self.changeListener.resume();
        self.emit('reset', {query:self.query});
        self.emit('change', {query:self.query});
      });
    },

    save: function(key, value) {
      this.savedQuery[key] = value;
      localStorage.setItem(this.saveScope, JSON.stringify(this.savedQuery));
    },

    read: function(key) {
      return this.savedQuery[key];
    },

    remove: function(key) {
      delete this.savedQuery[key];
      localStorage.setItem(this.saveScope, JSON.stringify(this.savedQuery));
    },

    applyFilterFromInput: function(node) {
      var name = this.getInputName(node);
      var value = this.getInputValue(node);
      this.save(name, value);
      var filters = {};
      if (name.substr(-2) == '[]') {
        filters[name.substr(0, name.length-2)] = [value];
      } else {
        filters[name] = value;
      }
      var remove = false;
      if (node.tagName == "INPUT" && node.type == "checkbox" && !node.checked) {
        remove = true;
      } else if (value === "") {
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
