define([
  "dojo/_base/declare",
  "dojo/_base/lang",
  "dijit/_WidgetBase",
  "dijit/_AttachMixin",
  "dijit/Calendar",
  "dojo/date",
  "dojo/date/locale",
  "put-selector/put"
], function(declare, lang, _WidgetBase, _AttachMixin, Calendar, date, locale, put) {
  return declare([_WidgetBase, _AttachMixin], {
    buildRendering: function() {
      this.inherited(arguments);
      this.domNode.type = "hidden";
      this.createCalendarNode();
    },
    postCreate: function() {
      this.createCalendar();
    },
    createCalendarNode: function() {
      this.calendarNode = put(this.domNode, "+div");
    },
    createCalendar: function() {
      this.calendar = new Calendar({
        onChange: lang.hitch(this, 'onChange'),
        isDisabledDate: function(d) {
          var d = new Date(d); d.setHours(0,0,0,0);
          var today = new Date(); today.setHours(0,0,0,0);
          return date.difference(d, today, "day") > 0;
        }
      }, this.calendarNode);
      this.calendar.startup();
    },
    onChange: function(value) {
      console.log(value);
      this.domNode.value = locale.format(value, {selector: 'date', datePattern: 'yyyy-MM-dd'});
    }
  });
});