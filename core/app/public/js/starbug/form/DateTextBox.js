define(["dojo/_base/declare", "dijit/form/DateTextBox", "dojo/date/locale", "dojo/on", "dojo/dom-class"], function(declare, DateTextBox, locale, on, domclass){
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
			if (this.textbox.value) {
				domclass.add(this.domNode, 'dijitFilled');
			} else {
				domclass.remove(this.domNode, 'dijitFilled');
			}
			on.emit(this.textbox, "change", {bubbles: true, cancelable: true});
		}
	});
});
