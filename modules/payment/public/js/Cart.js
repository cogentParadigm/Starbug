define([
	"dojo/_base/declare",
	"dijit/_WidgetBase",
	"dijit/_TemplatedMixin",
	"dijit/_WidgetsInTemplateMixin",
	"sb/store/Api",
	"dojo/text!./templates/Cart.html",
	"dgrid/GridFromHtml",
	"dgrid/Grid",
	"dgrid/Editor",
	"put-selector/put",
	"dojo/on",
	"dojo/query",
	"dojo/promise/all",
	"dojo/Deferred",
	"starbug/grid/columns/html",
	"payment/grid/columns/quantity",
	"payment/grid/columns/remove",
	"starbug/form/Address"
], function (declare, Widget, Templated, _WidgetsInTemplate, api, template, GridFromHtml, Grid, Editor, put, on, query, all, Deferred) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		query:{}, //parameters for the query
		products:null,
		shipping:null,
		results:[],
		grid:null,
		templateString: template, //the template
		widgetsInTemplate: true,
		postCreate:function() {
			var self = this;
			this.products = new api({model:'product_lines', action:'cart'});
			this.shipping = new api({model:'shipping_lines', action:'cart'});

			var EditableGrid = declare([GridFromHtml, Grid, Editor]);
			this.grid = new EditableGrid({editor:this, selectionMode:'none'}, this.gridNode);
			this.grid.startup();
			this.refresh();
		},
		refresh:function() {
			var self = this;
			all([this.products.filter(this.query).fetch()]).then(function(data) {
				var results = data[0];
				self.grid.refresh();
				self.totalsNode.innerHTML = '';
				self.results = results;
				self.grid.renderArray(results);
				var total = 0;
				var shipping_total = 0;
				for (var i in results) {
					if (!isNaN(i)) {
						total += parseInt(results[i].total);
					}
				}
				put(put(self.totalsNode, 'div.total', 'Subotal: '), 'strong', '$'+(total/100).toFixed(2));
				if (shipping_total) {
					put(put(self.totalsNode, 'div.total', 'Shipping: '), 'strong', '$'+(shipping_total/100).toFixed(2));
				}
				put(put(self.totalsNode, 'div.total', 'Total: '), 'strong', '$'+((total + shipping_total)/100).toFixed(2));
			});
		},
		remove:function(id, type) {
			var self = this;
			var store = false;
			if (type == 'product_lines') store = this.products;
			if (store) {
				store.remove(id).then(function(){
					self.refresh();
				});
			}
		},
		update:function() {
			var self = this;
			var data = {};
			for (var i in this.results) {
				if (i == 'totalLength') continue;
				var line = this.results[i];
				var row = this.grid.row(i);
				var qty = query('.field-qty .dgrid-input', row.element).attr('value')[0];
				data[row.data.id] = qty;
			}
			this.products.put(data, {action:'update'}).then(function() {
					self.refresh();
			});
		}
	});
});
