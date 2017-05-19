define(["dojo/_base/declare", "dgrid/extensions/Pagination"], function(declare, Pagination){
	return declare([Pagination], {
		pagingLinks: 2,
		firstLastArrows: true,
		previousNextArrows: true,
		pagingTextBox: false,
		pageSizeOptions: [10, 15, 25, 50],
		rowsPerPage:25
	});
});
