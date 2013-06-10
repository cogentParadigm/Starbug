define(["dojo/_base/declare", "starbug/form/editable/Editable", "dijit/form/Textarea", "sb/markdown"], function(declare, Editable, Editor, marked) {
	return declare([Editable], {
		editor:Editor,
		editorParams:{scrolOnFocus:false, validate:function(){return false}},
		postCreate:function() {
			this.inherited(arguments);
			this.displayNode.innerHTML = marked(this.get('value'));
		},
		onChange:function(value) {
			this.inherited(arguments);
			this.displayNode.innerHTML = marked(value);
		}
	});
});
