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
		selected:null,
		query:{}, //parameters for the query
		order:{},
		store:null,
		products:null,
		shipping:null,
		results:[],
		totals:[],
		grid:null,
		coupons_grid:null,
		giftcards_grid:null,
		templateString: template, //the template
		widgetsInTemplate: true,
		enable_shipping:true,
		shipping_method:'standard',
		mode:'cart',
		deferreds:null,
		target:'checkout',
		postCreate:function() {
			var self = this;

			if (this.mode == 'checkout') {
				this.checkout.style.display = 'block';
				this.submit.innerText = 'Continue to Payment';
				if (this.enable_shipping) {
					this.shipping_container.style.display = "block";
					this.billing_same_container.style.display = "block";
				}
			} else if (this.mode == 'payment') {
				this.submit.style.display = 'none';
			}
			this.submit.setAttribute('href', this.target);

			this.store = new api({model:'orders', action:'cart'});
			this.products = new api({model:'product_lines', action:'cart'});
			this.shipping = new api({model:'shipping_lines', action:'cart'});

			var EditableGrid = declare([GridFromHtml, Grid, Editor]);
			this.grid = new EditableGrid({editor:this, selectionMode:'none'}, this.gridNode);
			this.grid.startup();
			this.refresh();
		},
		startup: function() {
			var self = this;
			this.shipping_address.onSave = function(data, address) {
				var item = {shipping_address: address.item_id};
				if (self.billing_same.checked) item.billing_address = address.item_id;
				self.store.put(item, {action:'cart'}).then(function() {
					if (self.deferreds.shipping_address) self.deferreds.shipping_address.resolve(true);
				});
			};
			this.billing_address.onSave = function(data, address) {
				self.store.put({billing_address: address.item_id}, {action:'cart'}).then(function() {
					if (self.deferreds.billing_address) self.deferreds.billing_address.resolve(true);
				});
			};
			if (this.order.shipping_address) {
				this.shipping_address.item_id = this.order.shipping_address;
				this.shipping_address.show();
				if (this.order.billing_address == this.order.shipping_address) {
					this.billing_same.checked = true;
					this.billing_address.domNode.style.display = 'none';
				}
			}
			if (this.order.billing_address && !this.billing_same.checked) {
				this.billing_address.item_id = this.order.billing_address;
				this.billing_address.show();
			}
			on(this.billing_same, 'change', function() {
				if (this.checked) {
					self.billing_address.domNode.style.display = 'none';
				} else {
					self.billing_address.domNode.style.display = 'block';
				}
			});
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
			if (type == 'product') store = this.products;
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
		update_shipping: function(e) {
			this.shipping_method = e.target.value;
			this.refresh();
		},
		next: function(e) {
			if (this.mode != 'checkout') return true;
			e.preventDefault();
			this.deferreds = {};
			var saves = [];
			if (!this.billing_same.checked) {
				saves.push(this.save_address(this.billing_address, 'billing_address'));
				saves.push(this.save_address(this.shipping_address, 'shipping_address'));
			} else {
				saves.push(this.save_address(this.shipping_address, 'shipping_address', 'billing_address'));
			}
			all(saves).then(function() {
				window.location = e.target.href;
			});
		},
		save_address: function(address, key, extra) {
			var deferred = new Deferred();
			if (address.formNode) address.execute();
			else if (address.item_id) {
				var data = {};
				data[key] = address.item_id;
				if (extra) data[extra] = address.item_id;
				this.store.put(data, {action:'cart'}).then(function() {
					deferred.resolve(true);
				});
			} else {
				console.log('reject');
				deferred.reject('error');
			}
			this.deferreds[key] = deferred;
			return deferred.promise;
		},
		redeem_coupon: function(e) {
			e.preventDefault();
			var self = this;
			this.coupons.put({code:this.coupon_key.value}, {action:'cart'}).then(function(result) {
				if (result.errors) {
					alert(result.errors[0].errors[0]);
				} else {
					self.refresh();
				}
			});
		},
		redeem_giftcard: function(e) {
			e.preventDefault();
		}
	});
});
