define(["dojo", "dijit", "dojox", "starbug", "dojo/text!./templates/Comment.html", "dijit/_Widget", "dijit/_Templated", "dojo/date/locale", "dojox/image/Lightbox"], function(dojo, dijit, dojox, starbug, template) {
	return dojo.declare('starbug.list.Comment', [dijit._Widget, dijit._Templated], {
		list: null,
		item: null,
		files:null,
		templateString: template,
		widgetsInTemplate: true,
		postCreate: function() {
			this.inherited(arguments);
			dojo.connect(this.deleteButton, 'onclick', dojo.hitch(this.list, 'deleteItem', this.item.id));
			dojo.connect(this.editButton, 'onclick', dojo.hitch(this.list, 'editItem', this.item.id));
			dojo.connect(this.helpfulButton, 'onclick', dojo.hitch(this.list, 'helpful', this.item.id));
			dojo.connect(this.blockButton, 'onclick', dojo.hitch(this.list, 'block', this.item.id));
			dojo.connect(this.unblockButton, 'onclick', dojo.hitch(this.list, 'unblock', this.item.id));
			if (this.list.attachFiles) {
				this.files = new starbug.data.ObjectStore({
					apiQuery: "comments.files  select:files.*  where:comments.id="+this.item.id,
					onItem: dojo.hitch(this, 'addItem'),
					onChange: dojo.hitch(this, 'updateItem')
				});
			} else dojo.destroy(this.fileList);
			this.render();
		},
		addItem: function(file, query, editing) {
			if (file.status == '1') return;
			if (!editing) var div = dojo.create('div', {id:'file_'+file.id}, this.fileList, 'last');
			if (file.mime_type.split('/')[0] == 'image') {
				dojo.attr('file_'+file.id, 'innerHTML', '<a id="file_'+file.id+'_lightbox" href="'+WEBSITE_URL+'app/public/uploads/'+file.id+'_'+file.filename+'" group="comment_'+this.item.id+'"><img src="'+WEBSITE_URL+'app/public/php/phpthumb/phpThumb.php?w=100&src=/app/public/uploads/'+file.id+'_'+file.filename+'"/></a>');
				var lb = new dojox.image.Lightbox({href:WEBSITE_URL+'app/public/uploads/'+file.id+'_'+file.filename, group:'comment_'+this.item.id}, dojo.byId('file_'+file.id+'_lightbox'));
				lb.startup();
			} else dojo.attr('file_'+file.id, 'innerHTML', '<a id="file_'+file.id+'_link" href="'+WEBSITE_URL+'app/public/uploads/'+file.id+'_'+file.filename+'" target="_blank"><span class="caption">'+file.filename+'</span><img width="100" src="'+WEBSITE_URL+'app/public/images/file.png"/></a>');
		},
		updateItem: function(file, query) {
			if (file.status == '1') dojo.destroy('file_'+file.id);
			else this.addItem(file, query, true);
		},
		formatDate: function(date) {
			if (typeof date == "string") {
				if (date == '0000-00-00 00:00:00') return '';
				var t = date.split(/[- :]/);
				date = new Date(t[0], parseInt(t[1])-1, t[2], t[3], t[4], t[5]);
			}
			return dojo.date.locale.format(date, {datePattern: "EEE, d MMM yyyy 'at' h:mma", selector: "date"});
		},
		render: function() {
			if (this.item.image == '') {
				dojo.style(this.userAvatar, 'display', 'none');
				dojo.style(this.defaultAvatar, 'display', 'block');
			} else {
				dojo.style(this.defaultAvatar, 'display', 'none');
				dojo.style(this.userAvatar, 'display', 'block');
				dojo.attr(this.userAvatar, 'src', WEBSITE_URL+'app/public/php/phpthumb/phpThumb.php?w=64&src=/app/public/uploads/'+this.item.image);
			}
			if (this.item.user_id == this.list.currentUser) {
				dojo.style(this.deleteButton, 'display', 'block');
			}
			if (this.list.moderator == this.list.currentUser) {
				if (this.item.helpful == 0) dojo.style(this.helpfulButton, 'display', 'block');
				if (this.item.blocked == 1) dojo.style(this.unblockButton, 'display', 'block');
				else if (this.list.moderator != this.item.owner) dojo.style(this.blockButton, 'display', 'block');
			}
			this.author.innerHTML = this.item.user;
			this.date.innerHTML = this.formatDate(this.item.created);
			this.body.innerHTML = this.item.comment;
			if (this.item.status == 2) {
				if (this.list.moderator == this.list.currentUser) this.body.innerHTML += '<div><a class="positive" href="#approve" onclick="dijit.byId(\''+this.list.id+'\').approve('+this.item.id+')">Mark as read</a> <a href="#reject" class="negative" onclick="dijit.byId(\''+this.list.id+'\').reject('+this.item.id+')">Delete</a>';
			}
			if (this.item.helpful > 0) {
				this.body.innerHTML += '<div class="helpful">this was helpful</div>';
			}
		}
	});
