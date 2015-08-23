define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/_base/array",
	"dojo/request/xhr",
	"dojo/query",
	"dojo/dom-class",
	"dojo/dom-style",
	"dojo/request/iframe",
	"dijit/Dialog",
	"dijit/registry",
	"dojo/dom-form",
	"dojo/behavior"
], function(declare, lang, array, xhr, query, domclass, domstyle, iframe, Dialog, registry, domForm, behavior) {
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
		doLayout:false,
		layoutParams:{},
		showTitle:false,
		draggable:false,
		postCreate: function() {
			this.inherited(arguments);
			var layoutDefaults = {width:'90%', height:'90%', position:'fixed', top:'0', left:'0', right:'0', bottom:'0', margin:'auto', overflow:'auto'};
			for (var i in layoutDefaults) if (typeof this.layoutParams[i] == "undefined") this.layoutParams[i] = layoutDefaults[i];
			domstyle.set(this.domNode, this.layoutParams);
			if (this.showTitle == false) domstyle.set(this.titleBar, 'display', 'none');
			this.set('content', '');

		},
		_position: function() {},
		resize: function(dim) {
			/*
			 * EXPERIMENTAL SCROLL BEHAVIOR - TO ENABLE, REMOVE overflow:auto FROM THE DIALOG
			if (this.containerNode.clientHeight > this.domNode.clientHeight) {
				var max = this.containerNode.clientHeight -this.domNode.clientHeight;
				domstyle.set(this.containerNode, 'top', 0 - Math.min(window.scrollY, max) + 'px');
			}
			*/
			this._layoutChildren();
		},
		_onSubmit: function(evt){
			evt.preventDefault();
			this.post_data[evt.target.getAttribute('name')] = evt.target.getAttribute('value');
			this.execute();
			return false;
		},
		execute: function() {
			domclass.add(this.form, 'loading');
			query('.loading', this.form).style('display','block');
			var data = lang.delegate(domForm.toObject(this.form), this.post_data);
			query('.rich-text', this.form).forEach(function(node) {
				data[node.name] = window.tinyMCE.get(node.id).getContent();
			});
			var request_url = this.url+(this.crudSuffixes ? ((this.item_id) ? 'update/'+this.item_id : 'create') : '')+((this.format != false) ? '.xhr' : '');
			var token = '?';
			for (var i in this.get_data) {
				request_url += token+i+'='+this.get_data[i];
				token = '&';
			}
			iframe(
			request_url,
			{
				data: data,
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
		show: function(id, params) {
			params = params || {};
			this.inherited(arguments);
			if (id) this.item_id = id;
			else this.item_id = 0;
			var request_url = this.url+(this.crudSuffixes ? ((id) ? 'update/'+id : 'create') : '')+((this.format != false) ? '.xhr' : '');
			var token = '?';
			for (var i in this.get_data) {
				request_url += token+i+'='+this.get_data[i];
				token = '&';
			}
			for (var x in params) {
				request_url += token+x+'='+params[x];
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
			behavior.apply();
		}
	});
});
