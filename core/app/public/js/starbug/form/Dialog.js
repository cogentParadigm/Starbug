define(["dojo", "dojo/io/iframe", "dijit/Dialog"], function(dojo, iframe, Dialog) {
	return dojo.declare("starbug.form.Dialog", [Dialog], {
		dialog:null,
		url:'',
		callback:null,
		form:null,
		item_id: 0,
		post_data:{},
		postCreate: function() {
			this.inherited(arguments);
			this.set('content', '');
		},
		_onSubmit: function(evt){
			evt.preventDefault();
			this.execute();
			return false;
		},
		execute: function() {
			dojo.addClass(this.form, 'loading');
			dojo.query('.loading', this.form).style('display','block');
			iframe.send({
				form: this.form,
				url: this.url+((this.item_id) ? 'update/'+this.item_id : 'create')+'.xhr',
				content: this.post_data,
				handleAs: 'html',
				load: dojo.hitch(this, 'load')
			});
		},
		upload: function(evt) {
			dojo.query('.submit', this.form).attr('disabled','true');
			dojo.addClass(this.form, 'loading');
			this.post_data['action[files]'] = 'prepare';
			this.execute();
			delete this.post_data['action[files]'];
		},
		load: function(data) {
			res = dojo.byId("dojoIoIframe").contentWindow.document.body.innerHTML;
			this.loadForm(res);
			if (dojo.hasClass(this.form, 'submitted')) {
				this.hide();
				if (this.callback != null) this.callback(data, this);
			}
		},
		setValues: function(args) {
			for (var i in args) {
				dojo.query('[name="'+i+'"]').attr('value', args[i]);
			}
		},
		show: function(id) {
			this.inherited(arguments);
			if (id) this.item_id = id;
			else this.item_id = 0;
			dojo.xhrGet({
				url: this.url+((id) ? 'update/'+id : 'create')+'.xhr',
				content:this.post_data,
				load: dojo.hitch(this, 'loadForm')
			});
		},
		hide: function(evt) {
			if (evt) evt.preventDefault();
			this.inherited(arguments);
		},
		remove: function(model, id) {
			var args = {};
			args['action['+model+']'] = 'delete';
			args[model+'[id]'] = id;
			sb.post(args, 'return confirm(\'Are you sure you want to delete this item?\')');
		},
		loadForm: function(data) {
			this.set('content', data);
			this.form = dojo.query('form', this.domNode)[0];
			dojo.query('form', this.domNode).on('submit', function(evt) {evt.preventDefault();});
			dojo.query('.submit, [type=\"submit\"]', this.form).attr('onclick', '').on('click', dojo.hitch(this, '_onSubmit'));
			dojo.query('.cancel', this.form).attr('onclick', '').on('click', dojo.hitch(this, 'hide'));
			dojo.query('input[type="file"]', this.form).on('change', dojo.hitch(this, 'upload'));
		}
	});
});
