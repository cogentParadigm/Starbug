define([
	"dojo",
	"starbug",
	"sb",
	"sb/data",
	"starbug/store/Api",
	"dgrid/GridFromHtml",
	"dgrid/Keyboard",
	"dgrid/Selection"
], function(dojo, starbug, sb, data, api, GridFromHtml, Keyboard, Selection){
var Grid = dojo.declare('starbug.grid.Grid', [GridFromHtml, Keyboard, Selection], {
	constructor: function(args) {
		this.store = sb.get(args.model, args.action);
	}
});
return Grid;
});

