define([
	"dojo/_base/declare",
	"./Cart",
	"dojo/text!./templates/Order.html"
], function (declare, Cart, template) {
	return declare([Cart], {
		templateString: template, //the template
	});
});
