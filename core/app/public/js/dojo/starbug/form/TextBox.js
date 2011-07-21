define("starbug/form/TextBox", ["dojo", "dijit", "dijit/form/_FormWidget", "dijit/form/TextBox"], function(dojo, dijit) {

dojo.declare("starbug.form.TextBox", dijit.form._FormValueWidget, {
		templateString: 'input',
		type: 'text'
	}
);

return starbug.form.TextBox;
});
