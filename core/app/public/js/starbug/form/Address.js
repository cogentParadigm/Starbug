define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/request/xhr",
	"dojo/query",
	"dijit/_WidgetBase",
	"dijit/_TemplatedMixin",
	"put-selector/put",
	"dojo/dom-form",
	"dojo/dom-class"
], function(declare, lang, xhr, query, Widget, Templated, put, domForm, domclass) {
	return declare([Widget, Templated], {
		dialog:null,
		url:'',
		formNode:null,
		countryNode:null,
		item_id: null,
		templateString:'<div class="address-form"></div>',
		country:'US',
		store:null,
		postCreate: function() {
			this.url = WEBSITE_URL + 'address/form/';
			this.show(this.item_id);
		},
		_onSubmit: function(evt){
			evt.preventDefault();
			this.execute();
			return false;
		},
		execute: function() {
			put(this.formNode, '.loading');
			var values = domForm.toObject(this.formNode);
			var request_url = this.url + this.country;
			xhr(request_url, {method:'POST', data:values}).then(lang.hitch(this, 'load'));
		},
		onSave: function(data, self) {

		},
		load: function(data) {
			this.loadForm(data);
			if (this.item_id) {
				this.onSave(data, this);
			}
		},
		setValues: function(args) {
			for (var i in args) {
				query('[name="'+i+'"]').attr('value', args[i]);
			}
		},
		show: function(edit) {
			this.inherited(arguments);
			var request_url = this.url + this.country;
			var options = {query:{}};
			if (this.item_id) options.query.id = this.item_id;
			if (edit) options.query.edit = true;
			xhr(request_url, options).then(lang.hitch(this, 'loadForm'));
		},
		update: function(evt) {
			this.country = evt.target.options[evt.target.selectedIndex].value;
			this.show();
		},
		edit: function() {
			this.show(true);
		},
		loadForm: function(data) {
			var self = this;
			this.domNode.innerHTML = data;
			this.formNode = query('form', this.domNode)[0];
			query('form', this.domNode).on('submit', function(evt) {evt.preventDefault();});
			query('.submit, [type=\"submit\"]', this.domNode).attr('onclick', '').on('click', lang.hitch(this, '_onSubmit'));
			//query('.cancel', this.form).attr('onclick', '').on('click', lang.hitch(this, 'cancel'));
			query('.country-field', this.domNode).on('change', lang.hitch(this, 'update'));
			query('.address-value', this.domNode).forEach(function(node) {
				self.item_id = node.value;
			});
			query('a.edit').on('click', lang.hitch(this, 'edit'));
		}
	});
});
