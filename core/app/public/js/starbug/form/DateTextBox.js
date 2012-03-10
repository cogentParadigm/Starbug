define(["dojo/_base/declare", "dijit/form/DateTextBox", "dojo/date/locale", "dojo/dom"], function(declare, DateTextBox, locale, dom){
	declare("starbug.form.DateTextBox", DateTextBox, {
		sqlFormat: {selector: 'date', datePattern: 'yyyy-MM-dd'},
		value: "", // prevent parser from trying to convert to Date object
		postMixInProperties: function(){
			this.inherited(arguments);
			// convert value to Date object
			this.value = locale.parse(this.value.split(' ')[0], this.sqlFormat);
		}
	});
});
