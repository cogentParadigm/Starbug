define(["dojo/_base/declare", "dojo/_base/lang", "dojo/dom-construct", "dojo/dom-style", "sb", "dijit/InlineEditBox", "sb/store/Api"], function(declare, lang, domConstruct, domStyle, sb, InlineEdit, Api) {
	return declare([InlineEdit], {
		model:false,
		store:false,
		field:'',
		object_id:0,
		autoSave:false,
		storeOnSave:true,
		postCreate: function() {
			this.store = this.store || new Api({model:this.model, action:'admin'});
		},
		onChange:function(value) {
			this.inherited(arguments);
			value = this.wrapperWidget.editWidget.get('value');
			if (this.store && this.field && this.storeOnSave) {
				var data = {id:this.object_id};
				data[this.field] = value;
				this.store.put(data).then(function(result) {
					if (result.errors) alert(result.errors[0].errors[0]);
				});
			}
		},
		startup:function() {
			this.inherited(arguments);
			// Placeholder for edit widget
			// Put place holder (and eventually editWidget) before the display node so that it's positioned correctly
			// when Calendar dropdown appears, which happens automatically on focus.
			var placeholder = domConstruct.create("span", null, this.domNode, "before");

			// Create the editor wrapper (the thing that holds the editor widget and the save/cancel buttons)
			var Ewc = typeof this.editorWrapper == "string" ? lang.getObject(this.editorWrapper) : this.editorWrapper;
			this.wrapperWidget = new Ewc({
				value: this.value,
				buttonSave: this.buttonSave,
				buttonCancel: this.buttonCancel,
				dir: this.dir,
				lang: this.lang,
				tabIndex: this._savedTabIndex,
				editor: this.editor,
				inlineEditBox: this,
				sourceStyle: domStyle.getComputedStyle(this.displayNode),
				save: lang.hitch(this, "save"),
				cancel: lang.hitch(this, "cancel"),
				textDir: this.textDir
			}, placeholder);
			this.wrapperWidget.startup();
			this.wrapperWidget.editWidget.set('value', this.get('value'));
		}
	});
});
