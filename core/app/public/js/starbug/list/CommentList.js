define([
	"dojo",
	"dijit",
	"dojox",
	"starbug",
	"dojo/text!./templates/CommentList.html",
	"starbug/list/Comment",
	"dijit/_Widget", "dijit/_Templated",
	"dijit/Editor",
	"starbug/data/ObjectStore",
	"dojox/form/Uploader",
	"dojox/form/uploader/plugins/HTML5",
	"dojox/form/uploader/FileList"
], function (dojo, dijit, dojox, starbug, template) {
	return dojo.declare('starbug.list.CommentList', [dijit._Widget, dijit._Templated], {
		itemClass: starbug.list.Comment,
		store: null,
		object_type:'',
		object_id: null,
		where: '',
		items:{},
		currentUser:0,
		moderator:0,
		editing:0,
		files:[],
		attachFiles:false,
		readOnly:false,
		blocked:false,
		templateString: template,
		widgetsInTemplate: true,
		postCreate: function() {
			this.inherited(arguments);
			if (this.blocked) this.readOnly = true;
			if (this.readOnly) {
				this.editor.destroy();
				this.button.destroy();
			}
			if (this.object_id != null) this.where = this.object_type+'_comments.object_id='+this.object_id;
			this.store = new starbug.data.ObjectStore({
				apiQuery: this.object_type+'_comments.users.files  select:'+this.object_type+"_comments.*,concat(users.first_name, ' ', users.last_name) as user,users.id as user_id,concat(files.id, '_', files.filename) as image  join:left  where:"+this.where,
				onItem: dojo.hitch(this, 'addItem'),
				onChange: dojo.hitch(this, 'updateItem')
			});
			this.store.fetch({onItem:dojo.hitch(this, 'addItem')});
			if (this.attachFiles) {
				this.connect(this.uploader, "onBegin", function() {
					dojo.attr(this.fileStatus, 'innerHTML', '<img src="'+WEBSITE_URL+'/app/public/images/loading.gif" width="70"/>');
				});
				dojo.connect(this.uploader, 'onComplete', dojo.hitch(this, 'onUpload'));
			}
		},
		addItem: function(item) {
			if (item.status == '1') return;
			var li = dojo.create('li', {id:this.object_type+'_comment_'+item.id}, this.containerNode, 'last');
			this.items[item.id] = new this.itemClass({item:item, list:this}, li);
			dojo.attr(this.statusNode, 'innerHTML', '');
		},
		updateItem: function(item) {
			if (item.status == '1') this.items[item.id].destroy();
			else {
				this.items[item.id].item = item;
				this.items[item.id].render();
			}
			if (this.editing == item.id) {
				dojo.style(this.items[this.editing].containerNode, 'display', 'block');
				this.editing = 0;
				dojo.attr(this.statusNode, 'innerHTML', '');
			}
		},
		newItem: function() {
			dojo.attr(this.statusNode, 'innerHTML', '<img src="'+WEBSITE_URL+'/app/public/images/loading.gif" width="70"/>');
			var args = {};
			args['action['+this.object_type+'_comments]'] = 'create';
			args[this.object_type+'_comments[comment]'] = dojo.attr(dojo.query('#dijitEditorBody', this.editor.document.body)[0], 'innerHTML');
			if (this.object_id != null) args[this.object_type+'_comments[object_id]'] = this.object_id;
			if (this.editing != 0) args[this.object_type+'_comments[id]'] = this.editing;
			if (this.files.length > 0) {
				args[this.object_type+'_comments[files]'] = '';
				dojo.forEach(this.files, function(f, i) {
					if (args[this.object_type+'_comments[files]'] != '') args[this.object_type+'_comments[files]'] += ',';
					args[this.object_type+'_comments[files]'] += f.id;
				}, this);
			}
			dojo.xhrPost({
				url: WEBSITE_URL+'api/'+this.object_type+'_comments.users.files.json?select='+encodeURIComponent(this.object_type+"_comments.*,concat(users.first_name, ' ', users.last_name) as user,users.id as user_id,concat(files.id, '_', files.filename) as image")+'&join=left&where='+this.where.replace('=', '%3D'),
				content: args,
				handleAs:'json',
				load: dojo.hitch(this, function(data) {
					if (data.errors) error = true;
					else this.addItem(data[0]);
					dojo.attr(dojo.query('#dijitEditorBody', this.editor.document.body)[0], 'innerHTML', '');
					this.files = [];
					dojo.attr(this.fileList, 'innerHTML', '');
					dojo.attr(this.statusNode, 'innerHTML', '');
				})
			});
		},
		editItem: function(id) {
			this.editing = id;
			dojo.style(this.items[id].containerNode, 'display', 'none');
			this.editor.attr('value', dojo.attr(this.items[id].body, 'innerHTML'));
			dojo.attr(this.statusNode, 'innerHTML', '<strong>Editing this comment...</strong>');
		},
		deleteItem: function(id) {
			if (confirm('Are you sure you want to delete this comment?')) {
				this.store.fetchItemByIdentity({
					identity: id,
					onItem: dojo.hitch(this, function(item) {
						this.store.deleteItem(item);
						this.items[id].destroy();
					})
				});
			}
		},
		onUpload: function(fileArray) {
			dojo.attr(this.fileStatus, 'innerHTML', '');
			dojo.forEach(fileArray, function(f, i){
				this.files.push(f);
				dojo.place('<div class="fileItem" style="padding:5px"><strong>'+f.original_name+'</strong></div>', this.fileList);
			}, this)
		},
		xhr: function(action, data, callback) {
			var args = {};
			args['action['+this.object_type+'_comments]'] = action;
			for (var i in data) args[this.object_type+'_comments['+i+']'] = data[i];
			dojo.xhrPost({
				url: WEBSITE_URL+'api/'+this.object_type+'_comments.users.files.json?select='+encodeURIComponent(this.object_type+"_comments.*,concat(users.first_name, ' ', users.last_name) as user,users.id as user_id,concat(files.id, '_', files.filename) as image")+'&join=left&where='+encodeURIComponent(this.where),
				content: args,
				handleAs:'json',
				load: callback
			});
		},
		helpful: function(id) {
			this.xhr('helpful', {'id':id}, dojo.hitch(this, function(data) {
				if (data.errors) {
					error = true;
				} else {
					this.updateItem(data[0]);
					window.location.reload();
				}
			}));
		},
		block: function(id) {
			if (confirm('Are you sure you want to block this user?\nAll other pending comments for this user will be removed.')) {
				this.xhr('block', {'id':id}, dojo.hitch(this, function(data) {
					if (data.errors) error = true;
					else window.location.reload();
				}));
			}
		},
		unblock: function(id) {
			this.xhr('unblock', {'id':id}, dojo.hitch(this, function(data) {
				if (data.errors) error = true;
				else window.location.reload();
			}));
		},
		approve: function(id) {
			this.xhr('approve', {'id':id}, dojo.hitch(this, function(data) {
				if (data.errors) {
					error = true;
				} else {
					this.updateItem(data[0]);
					window.location.reload();
				}
			}));
		},
		reject: function(id) {
			this.xhr('reject', {'id':id}, dojo.hitch(this, function(data) {
				if (data.errors) {
					error = true;
				} else {
					this.updateItem(data[0]);
					window.location.reload();
				}
			}));		
		}
	});
});
