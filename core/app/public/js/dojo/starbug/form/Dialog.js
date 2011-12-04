define([
	"dojo",
	"dijit.Dialog"
], function(dojo, Dialog) {
	return dojo.declare("starbug.form.Dialog", [Dialog], {
		dialog:null,
		url:'',
		callback:null,
		form:null,
		model:'',
		action:'create',
		updateUrl: '',
		item_id: 0,
		persist:false,
		postCreate: function() {
			this.inherited(arguments);
			this.form = dojo.query('form', this.domNode)[0];
			dojo.query('form', this.domNode).connect('onsubmit', dojo.hitch(this, '_onSubmit'));
			dojo.query('.cancel', this.form).connect('onclick', this, 'hide');
			dojo.query('.error', this.form).forEach(dojo.destroy);
			if (this.url == '') this.url = WEBSITE_URL+'api/'+this.model+'.json';
			var list = null;
			if (list = dojo.query('[name="action['+this.model+']"]', this.form)) list.attr('value', this.action);
			else dojo.create('input', {'type':'hidden', 'name':'action['+this.model+']', 'value':this.action}, this.form);
		},
		_onSubmit: function(evt){
			evt.preventDefault();
			this.execute(this.get('value'));
			return false;
		},
		execute: function(args) {
			dojo.query('.error', this.form).forEach(dojo.destroy);
			dojo.addClass(this.form, 'loading');
			dojo.xhrPost({
				url: this.url,
				form: this.form,
				handleAs: 'json',
				load: dojo.hitch(this, 'load')
			});
		},
		load: function(data) {
			dojo.removeClass(this.form, 'loading');
			if (data.errors) {
				var node = null;
				var span = null;
				for (var field in data.errors) {
					field = data.errors[field];
					node = dojo.query('[name *= "'+field.field+'"]', this.form);
					if (node != null) {
						node = node[0];
						for (var e in field.errors) {
							span = '<span class="error">'+field.errors[e]+'</span>';
							dojo.place(span, node, "before");
						}
					}
				}
			} else {
				this.hide();
				if (this.callback != null) this.callback(data, this);
			}
		},
		setValues: function(args) {
			for (var i in args) {
				dojo.query('[name="'+this.model+'['+i+']"]').attr('value', args[i]);
			}
		},
		show: function(id) {
			this.inherited(arguments);
			if (id) {
				this.item_id = id;
				dojo.addClass(this.form, 'loading');
				if (this.updateUrl) {
					dojo.xhrGet({
						url: this.updateUrl+id+'.xhr',
						load: dojo.hitch(this, 'loadForm')
					});
				} else {
					if (dojo.query('[name="'+this.model+'[id]"]', this.form) == false) dojo.create('input', {'type':'hidden', 'name':this.model+'[id]'}, this.form);
					dojo.xhrGet({
						url: this.url+'?where='+this.model+'.id='+id+'&select='+this.model+'.*',
						handleAs: 'json',
						load: dojo.hitch(this, function(data) {
							this.setValues(data.items[0]);
							dojo.removeClass(this.form, 'loading');
						})
					});
				}
			} else if (!this.persist) {
				dojo.query('[name^="'+this.model+'"]', this.form).attr('value', '');
				dojo.query('[name="'+this.model+'[id]"]').forEach(dojo.destroy);
			}
		},
		loadForm: function(data) {
			this.set('content', data);
			this.form = dojo.query('form', this.domNode)[0];
			dojo.query('form', this.domNode).connect('onsubmit', dojo.hitch(this, '_onSubmit'));
			dojo.query('.cancel', this.form).connect('onclick', this, 'hide');
		}
	});
});
