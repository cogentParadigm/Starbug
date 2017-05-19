define(["dojo/_base/declare", "dgrid/Selector"], function(declare, Selector){
	return declare([Selector], {
		addUiClasses: false,
		allowSelectAll: true,
		deselectOnRefresh: false,
		selectionMode: "multiple",
		selectionDelegate: ".dgrid-selector"
	});
});
