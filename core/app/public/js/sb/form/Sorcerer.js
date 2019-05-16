define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./Wizard",
  "dojo/query",
  "dojo/dom-class",
  "dojo/dom-form"
], function(declare, lang, Wizard, query, domClass, domForm) {
  return declare([Wizard], {
    onSubmit: false,
    getValues: function(values) {
      values = values || {};
      this.forms.forEach(lang.hitch(this, function(node, i) {
        values = lang.mixin(domForm.toObject(node), values);
      }));
      return values;
    },
    submit: function(values) {
      var result = this.inherited(arguments);
      if (false !== this.onSubmit) {
        result.then(lang.hitch(this, 'onSubmit'));
      }
      return result;
    },
    loadForm: function(data) {
      if (data) this.set("content", data);
      this.forms = query('form', this.domNode).forEach(lang.hitch(this, function(node, i) {
        domClass.add(node, this.transition[0]);
        if (i > 0) {
          node.style.display = "none";
        }
      }));
      this.inherited(arguments, []);
      query('form [data-step]', this.domNode).on('click', lang.hitch(this, 'goToStep'));
    },
    goToStep: function(e) {
      var fromStep = this.formNode.getAttribute("data-step");
      var toStep = e.currentTarget.getAttribute("data-step");
      this.forms.shift().style.display = "none";
      this.forms.splice(fromStep - 1, 0, this.formNode);
      this.formNode = this.forms.splice(toStep - 1, 1).pop();
      this.forms.unshift(this.formNode);
      this.formNode.style.display = "";
    }
  });
})