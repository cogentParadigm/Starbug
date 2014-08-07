define(["dojo/_base/declare", "dojo/_base/lang", "dojo/_base/array", "dojo/request/xhr", "dojo/query", "dojo/dom-class", "dojo/request/iframe", "dijit/Dialog", "dijit/registry"], function(declare, lang, array, xhr, query, domclass, iframe, Dialog, registry) {
	return declare([Dialog], {
		dialog:null,
		url:'',
		callback:null,
		form:null,
		item_id: 0,
		post_data:{},
		get_data:{},
		crudSuffixes:true,
		format:"xhr",
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
			domclass.add(this.form, 'loading');
			query('.loading', this.form).style('display','block');
			iframe(
			this.url+(this.crudSuffixes ? ((this.item_id) ? 'update/'+this.item_id : 'create') : '')+((this.format != false) ? '.xhr' : ''),
			{
				form: this.form,
				data: this.post_data,
				handleAs:'html'
			}).then(lang.hitch(this, 'load'));
		},
		upload: function(evt) {
			query('.submit', this.form).attr('disabled','true');
			domclass.add(this.form, 'loading');
			this.post_data['action[files]'] = 'prepare';
			this.execute();
			delete this.post_data['action[files]'];
		},
		load: function(data) {
			this.loadForm(data.body.innerHTML);
			if (domclass.contains(this.form, 'submitted')) {
				this.hide();
				if (this.callback != null) this.callback(data, this);
			}
		},
		setValues: function(args) {
			for (var i in args) {
				query('[name="'+i+'"]').attr('value', args[i]);
			}
		},
		show: function(id) {
			this.inherited(arguments);
			if (id) this.item_id = id;
			else this.item_id = 0;
			var request_url = this.url+(this.crudSuffixes ? ((id) ? 'update/'+id : 'create') : '')+((this.format != false) ? '.xhr' : '');
			var token = '?';
			for (var i in this.get_data) {
				request_url += token+i+'='+this.get_data[i];
				token = '&';
			}
			xhr(request_url, {data:this.post_data}).then(lang.hitch(this, 'loadForm'));
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
			array.forEach(registry.findWidgets(this.domNode), function(w) {
				w.destroyRecursive();
			});
			this.set('content', data);
			this.form = query('form', this.domNode)[0];
			query('form', this.domNode).on('submit', function(evt) {evt.preventDefault();});
			query('.submit, [type=\"submit\"]', this.form).attr('onclick', '').on('click', lang.hitch(this, '_onSubmit'));
			query('.cancel', this.form).attr('onclick', '').on('click', lang.hitch(this, 'hide'));
			query('input[type="file"]', this.form).on('change', lang.hitch(this, 'upload'));
		}
	});
});
