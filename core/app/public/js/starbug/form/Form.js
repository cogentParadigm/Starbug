define("starbug/form/Form", ["dojo", "dijit", "dijit/_Widget", "dijit/_Templated", "dijit/form/_FormMixin", "dijit/layout/_ContentPaneResizeMixin", "dijit/form/Form"], function(dojo, dijit) {
	return dojo.declare("starbug.form.Form", [dijit.form.Form], {
			model:'',
			action:'',
			values:{},
			errors: {},
			text: function(args, position, node) {
				if (!position) position = 'last';
				if (!node) node = this.containerNode;
				var textField = new starbug.form.TextBox(sb.star("name:"+args));
				textField.placeAt(node, position);
				return textField;
			},
			password: function(args, position, node) {
				if (!position) position = 'last';
				if (!node) node = this.containerNode;
				var textField = new starbug.form.TextBox(sb.star("type:password  name:"+args));
				textField.placeAt(node, position);
				return textField;
			}
		}
	);
});
