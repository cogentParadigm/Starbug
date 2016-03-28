/*
This widget is used within the Starbug framework simply by adding parameters to the Starbug text() function.

ex: text("location  label:Location (City, ST)  autocomplete:sb.get('cities')  data-dojo-props:query:{'select':'id,concat(accent_city,\', \',region) city'}");

In this example, text() receives two additional parameters:
- autocomplete
	- This parameter tells starbug to use the autocomplete widget
	- This parameter takes as a required value a string to set the store for the widget.
- data-dojo-props
	- This parameter is used to set optional parameters for the widget: query and limit.
*/
define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dijit/_WidgetBase",
	"dijit/_AttachMixin",
	"dijit/Tooltip",
	"sb",
	"dgrid/OnDemandList",
	"put-selector/put",
	"dojo/on",
	"dojo/dom",
	"dojo/dom-class",
	"dojo/dom-style",
	"dojo/query",
	"xstyle/css!./css/autocomplete.css"
], function (declare, lang, Widget, Templated, Tooltip, sb, List, put, on, dom, domclass,domstyle,query) {
	return declare([Widget, Templated], {
		store: null,	// a string setting the store for the widget
						// ex: sb.get('uris', 'select');
		limit:1, 		// how many characters must be entered before the list of valies will be displayed
		placeholder: '',
		nodeHeight:0,
		query:{}, 	 	// {'select':'id,concat(accent_city,\', \',region) city','where':'!(status&1)'}
		listNode:null, 	//attached in the template, the node for the dgrid OnDemandList
		list:null, 	 	//the dgrid OnDemandList that holds the list of comments
		hiddenInput:null,
		widgetsInTemplate: true,
		interval:null,
		filterAttrName:'keywords',
		postCreate:function() {
			var self = this;

			self.hiddenInput = put(self.domNode.parentNode, 'input[type=hidden]');
			self.hiddenInput.name = self.domNode.name;
			self.hiddenInput.value = self.domNode.value;
			if (self.domNode.disabled) self.hiddenInput.disabled = self.domNode.disabled;
			self.domNode.name = self.domNode.value = "";

			self.domNode.autocomplete = "off";

			//list
			self.listNode = put(self.domNode.parentNode,'div.autocomplete-list.dgrid-autoheight');

			//set styles
			var w = domstyle.get(self.domNode,'width');
			//w += domstyle.get(self.domNode,'padding-left');
			//w += domstyle.get(self.domNode,'padding-right');
			self.listNode.style.width = w+'px';
			var bottom = domstyle.get(self.domNode,'margin-bottom');
			domstyle.set(self.domNode,'margin-bottom','0px');
			self.domNode.parentNode.style.marginBottom = bottom+'px';

			//instantiate a dgrid on demand list
			this.list = new List({

				rowHeight: 13,
				collection: self.store.filter(self.query),
				keepScrollPosition:true,
				renderRow: function(object, options){
					console.log(object);
					var node = put('div.autocomplete-item',object.label);
					on(node, 'click', function(e) {
						self.domNode.value = object.label;
						self.hiddenInput.value = object.id;
						var a = setTimeout(function() {domclass.remove(self.listNode,'show');},200);
					});
					return node;
				}

			}, this.listNode);
			this.list.startup();

			/*
			on(self.domNode, 'focus', function() {
				if(self.placeholder && self.placeholder == self.domNode.value) {
					self.domNode.value = '';
				}
			});
			*/

			on(self.domNode, 'keyup,click', function(e) {
				clearTimeout(self.interval);
				var keyCode = (window.event) ? e.which : e.keyCode;
				var valid =
					(keyCode == 8)					 || // backspace
					(keyCode > 47 && keyCode < 58)   || // number keys
					(keyCode > 64 && keyCode < 91)   || // letter keys
					(keyCode > 95 && keyCode < 112)  || // numpad keys
					(keyCode > 185 && keyCode < 193) || // ;=,-./` (in order)
					(keyCode > 218 && keyCode < 223);   // [\]' (in order)
				if (valid) self.interval = setTimeout(function() {self.resetList(e);}, 500);
				if (self.domNode.value === "") self.hiddenInput.value = "";
			});

			on(self.domNode, 'blur', function(e) {
				self.hideList(e);
				/*
				if(self.placeholder && '' === self.domNode.value) {
					self.domNode.value = self.placeholder;
				}
				*/
			});
			if (self.hiddenInput.value !== "") {
				self.store.filter({id:parseInt(self.hiddenInput.value)}).fetch().then(function(results) {
					if (results.length) {
						self.domNode.value = results[0].label;
					}
				});
			}

		},

		// repopulate the list when called
		resetList: function(evt) {
			var self = this;
			var city = self.domNode.value.replace(',','');
			if(city.length >= self.limit) {
				self.query[self.filterAttrName] = city;
				domclass.add(self.listNode,'show');
			} else {
				self.query[self.filterAttrName] = null;
				domclass.remove(self.listNode,'show');
			}
			self.list.set('collection', self.store.filter(self.query));
		},

		hideList: function(evt) {
			var self = this;
			setTimeout(function() {
				if(!evt.relatedTarget || ('dgrid-scroller' != evt.relatedTarget.className && 'dgrid' != document.activeElement.className.substr(0,4)))
					domclass.remove(self.listNode,'show');
			},200);
		}
	});
});
