define([
	"dojo/_base/declare",
	"dgrid/Grid"
], function (declare, Grid) {
	return declare([Grid], {
		keepScrollPosition:true,
		addUiClasses:false
	});
});
