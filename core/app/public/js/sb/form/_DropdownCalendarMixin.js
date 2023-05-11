define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "./_SelectionMixin",
  "./_DropdownMixin",
  "dijit/Calendar",
  "dojo/date/locale",
  "put-selector/put"
], function (declare, lang, _SelectionMixin, _DropdownMixin, Calendar, locale, put) {
  return declare([_SelectionMixin, _DropdownMixin], {
    calendarClass: false,
    localeFormat: {selector: 'date', datePattern: 'yyyy-MM-dd'},
    postMixInProperties: function() {
      this.inherited(arguments);
      this.calendarParams = this.calendarParams || {};
      this.calendarParams.delegate = this.calendarParams.delegate || this.selection;
      this.calendarParams.onChange = lang.hitch(this, "onChange");
      this.calendarClass = this.calendarClass || Calendar;
    },
    buildRendering: function() {
      this.inherited(arguments);
      this.calendarNode = put(this.dropdownNode, "div");
    },
    postCreate:function() {
      this.inherited(arguments);
      this.calendar = new this.calendarClass(this.calendarParams, this.calendarNode);
      this.calendar.startup();
    },
    startup: function() {
      this.inherited(arguments);
      if (this.domNode.hasAttribute("value")) {
        var value = this.domNode.getAttribute("value");
        if (this.isValidValue(value)) {
          this.selection.add([{id: value}]);
        }
      }
    },
    isValidValue: function(value) {
      return locale.parse(value, this.localeFormat) != null;
    },
    _setValueAttrHandler: function(value, suppress) {
      console.log(value);
      suppress = suppress || false;
      this.selection.selection.setData([]);
      if (this.isValidValue(value)) {
        this.selection.add([{id: value}]);
      } else {
        this.refresh({suppress: suppress});
      }
    },
    focusDropdown: function() {
      this.inherited(arguments);
      this.calendar.focus();
    },
    onChange: function(value) {
      this.calendar.delegate.add([{id: locale.format(value, this.localeFormat)}]);
    },
    renderSelection: function(items) {
      items = items || this.selection.getData();
      var value = this.get("displayedValue", items);
      if (value) value = value.split(' ')[0];
      if (this.calendar.value != null) {
        var calendarValue = locale.format(this.calendar.value, this.localeFormat)
      }
      if (typeof calendarValue != "undefined" && calendarValue != value) {
        var date = locale.parse(value, this.localeFormat);
        this.calendar.set("value", date);
      }
    },
    _getDisplayedValueAttr: function(items) {
      displayedValue =  this.get("value");
      return (displayedValue == "undefined") ? "" : displayedValue;
    },
    addStyles: function() {
      this.inherited(arguments);
      this.dropdownNode.style.width = "auto";
    }
  });
});""
