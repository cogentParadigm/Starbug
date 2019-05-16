define([
	"dojo/_base/declare",
	"dojo/_base/lang",
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
], function (declare, lang, Widget, Templated, _WidgetsInTemplate, Api, template, GridFromHtml, Grid, Editor, put, on, query, all, Deferred) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		query:{}, //parameters for the query
		products:null,
		shipping:null,
		results:[],
		grid:null,
		templateString: template, //the template
		widgetsInTemplate: true,
		mode: 'cart',
		checkoutUrl: '/checkout',
		refreshCount: 0,
		postCreate:function() {
			var self = this;
			this.products = new Api({model:'product_lines', action:'cart'});
			this.shipping = new Api({model:'shipping_lines', action:'cart'});
			this.shippingMethods = new Api({model: 'shipping_methods', action: 'select'});

			var EditableGrid = declare([GridFromHtml, Grid, Editor]);
			this.grid = new EditableGrid({editor:this, selectionMode:'none'}, this.gridNode);
			on(this.domNode, '[name=shipping_method]:change', function(e) {
				self.selectShippingMethod(this.value);
			});
		},
		startup: function() {
			this.grid.startup();
			this.refresh();
			if (this.mode == "cart") {
				put(this.actionsNode, 'a.pull-right.btn.btn-default[href=$] $', this.checkoutUrl, 'Checkout');
			}
		},
		refresh:function() {
			var self = this;
			all([this.products.filter(this.query).fetch(), this.shipping.filter(this.query).fetch()]).then(function(data) {
				var results = data[0];
				var shippingLines = data[1];
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
				for (var s in shippingLines) {
					if (!isNaN(s)) {
						shipping_total += parseInt(shippingLines[s].price);
					}
				}
				put(put(self.totalsNode, 'div.total', 'Subotal: '), 'strong', '$'+(total/100).toFixed(2));
				if (shipping_total) {
					put(put(self.totalsNode, 'div.total', 'Shipping: '), 'strong', '$'+(shipping_total/100).toFixed(2));
				}
				put(put(self.totalsNode, 'div.total', 'Total: '), 'strong', '$'+((total + shipping_total)/100).toFixed(2));

				self.shippingMethods.filter(self.query).fetch().then(lang.hitch(self, function(results) {
					if (shippingLines.length == 0 && this.refreshCount < 2) {
						this.selectShippingMethod(results[0].id);
					} else {
						put(this.shippingMethodsNode, {innerHTML: ''});
						for (var m = 0; m < results.length; m++) {
							var method = results[m];
							var group = put(this.shippingMethodsNode, 'div.radio label.f6.b input.mr2[type=radio][name=shipping_method][value=$]'+(shippingLines[0].method == method.id ? '[checked=checked]' : '')+'+$<', method.id, method.label);
							put(group, 'span.help-block', method.description);
						}
					}
					this.refreshCount++;
				}));
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
		},
		selectShippingMethod: function(id) {
			this.shippingMethods.put({id:id}, {action:'add'}).then(lang.hitch(this, function() {
				this.refresh();
			}));
		}
	});
});
