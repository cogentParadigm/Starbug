define(["dojo/_base/declare", "starbug/form/editable/Editable", "dijit/form/Select", "dojo/when"], function(declare, Editable, Editor, when) {
	return declare([Editable], {
		editor:Editor,
		storeOnSave:true,
		postCreate:function() {
			this.inherited(arguments);
			var self = this;
			this.editorParams.labelAttr = 'label';
			this.editorParams.onSetStore = function(store, items) {
				this.options.unshift({label:'&nbsp;', value:'NULL'});
				this._loadChildren();
				this.set('value', self.get('value'));
			};
			this.editorParams.sortByLabel = false;
		},
		startup:function() {
			
			this.inherited(arguments);
			window.w = this.wrapperWidget.editWidget;
			if (typeof this.wrapperWidget.editWidget._queryRes == "undefined") {
				this.displayNode.innerHTML = this.wrapperWidget.editWidget.get('displayedValue');
			} else {
				var self = this;
				when(this.wrapperWidget.editWidget._queryRes, function() {
					self.displayNode.innerHTML = self.wrapperWidget.editWidget.get('displayedValue');
				});
			}
		}
	});
});
