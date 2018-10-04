define(["dojo/_base/declare", "dijit/form/DateTextBox", "dojo/date/locale", "dojo/on"], function(declare, DateTextBox, locale, on){
	return declare(DateTextBox, {
		sqlFormat: {selector: 'date', datePattern: 'yyyy-MM-dd'},
		value: "", // prevent parser from trying to convert to Date object
		postMixInProperties: function(){
			this.inherited(arguments);
			// convert value to Date object
			this.value = locale.parse(this.value.split(' ')[0], this.sqlFormat);
		},
		serialize: function(dateObject, options){
			return locale.format(dateObject, this.sqlFormat).toUpperCase();
		},
		onChange: function(value) {
			on.emit(this.textbox, "change", {bubbles: true, cancelable: true});
		}
	});
});
