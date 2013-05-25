define([
	"dojo/_base/declare",
	"dojox/form/Uploader",
	"dojo/text!./templates/Uploader.html",
	"dojo/dom-style"
],function(declare, uploader, template, domstyle){
	return declare("starbug.form.Uploader", [dojox.form.Uploader], {
		templateString:template,
		category:null,
		_createInput: function(){
			this.inherited(arguments);
			domstyle.set(this.inputNode, 'left', '5px');
			domstyle.set(this.inputNode, 'top', '6px');
			domstyle.set(this.inputNode, 'right', 'auto');
			domstyle.set(this.inputNode, 'width', '91px');
			domstyle.set(this.inputNode, 'height', '30px');
			domstyle.set(this.inputNode, 'cursor', 'pointer');
		},
		upload: function(formData) {
			formData.category = this.category;
			this.inherited(arguments);
		}
	});

});
