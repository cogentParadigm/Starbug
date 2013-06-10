define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dijit/_WidgetBase",
	"dijit/_TemplatedMixin",
	"dijit/_WidgetsInTemplateMixin",
	"sb",
	"dojo/text!./templates/Browser.html",
	"dgrid/OnDemandList",
	"put-selector/put",
	"dojo/on",
	"dojo/query",
	"dojo/dom-class",
	"starbug/grid/Grid",
	"dijit/layout/BorderContainer",
	"dijit/layout/ContentPane",
	"dojox/image/Lightbox",
	"starbug/form/Uploader",
	"starbug/grid/columns/options",
	"starbug/grid/columns/filesize"
], function (declare, lang, Widget, Templated, _WidgetsInTemplate, sb, template, List, put, on, query, domclass, Grid) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		currentUser:0, //logged in user
		editing:0, //id of the comment being edited (if a comment is being edited)
		editingNode:null, //node of the comment being edited (if a comment is being edited)
		readOnly:false,
		query:{}, //parameters for the query
		list:null, //the dgrid OnDemandList that holds the list of comments
		clist:null, //the dgrid OnDemandList that holds the list of categories
		grid:null, //the details grid
		files:{},
		category:[],
		templateString: template, //the template (./templates/Comments.html)
		widgetsInTemplate: true,
		dialog:null,
		mode:'icons',
		modal:false,
		postCreate:function() {
			var self = this;

			//instantiate a dgrid on demand list
			this.list = new List({
        store: sb.get('files', 'list'),
        query: this.query,
        keepScrollPosition:true,
        renderRow: function(object, options){
					//the renderRow function will render our list item
					//and attach events within the item.
					//We can use the scope of this function to access
					//the target objects and nodes from within the event handlers
					
					//first put in a root node
					var node = put('div.media');

					
					if (object.mime_type.split('/')[0] == 'image') {
						var anchorNode = put(
							node,
							'a[href="javascript:;"][group="files"]',
							put('img.media-object[src="'+WEBSITE_URL+'var/public/thumbnails/100x100a1/'+object.id+'_'+object.filename+'"]')
						);
						put(anchorNode, 'div.name', object.filename);
						//var lb = new dojox.image.Lightbox({href:WEBSITE_URL+'app/public/uploads/'+object.id+'_'+object.filename, group:'files'}, anchorNode);
						//lb.startup();
					} else {
						var anchorNode = put(
							put(node, 'a[href="'+WEBSITE_URL+'app/public/uploads/'+object.id+'_'+object.filename+'"][target="_blank"]', put('span.caption', object.filename)),
							'img.media-object[width="100"][src="'+WEBSITE_URL+'app/themes/storm/public/images/file.png"]'
						);
					}
					
					on(anchorNode, 'click', function() {
						if (self.modal) {
							window.opener.SetUrl(WEBSITE_URL+'app/public/uploads/'+object.id+'_'+object.filename);
							self.close();
							return;
						}
						if (self.files[object.id]) {
							delete self.files[object.id];
							domclass.remove(node, 'selected');
						} else {
							for (var id in self.files) {
								domclass.remove(self.files[id][1], 'selected');
								delete self.files[id];
							}
							self.files[object.id] = [object, node];
							domclass.add(node, 'selected');
							self.contextMenu.style.display = 'block';
							/*
							self.selection.innerHTML = '';
							put(self.selection, 'h3', object.filename);
							put(self.selection, 'img.media-object[src="'+WEBSITE_URL+'app/public/uploads/'+object.id+'_'+object.filename+'"]'); 
							*/
						}
					});
					
					//delete
					on(put(node, 'a.close-btn[href="javascript:;"]', put('div.icon-remove')), 'click', function() {
						if (confirm('Are you sure you want to delete this image?')) {
							self.list.store.remove(object.id).then(function() {
								self.list.removeRow(node);
								if (self.grid != null) self.grid.removeRow(node);
							});
						}
					});
					
					//return the node
					return node;
        }
			}, this.listNode);
			this.list.startup();
			
			//instantiate a dgrid on demand list
			this.clist = new List({
        store: sb.get('terms', 'list'),
        query: {taxonomy:'files_category'},
        keepScrollPosition:true,
        renderRow: function(object, options){
					//the renderRow function will render our list item
					//and attach events within the item.
					//We can use the scope of this function to access
					//the target objects and nodes from within the event handlers
					
					//first put in a root node
					var node = put('div.category');
					
					on(put(node, 'a', object.term), 'click', function() {
						self.select_category(object, node);
					});
					
					if (object.selected || (self.category.length > 0 && self.category[0].id == object.id)) self.select_category(object, node);
					
					//return the node
					return node;
        }
			}, this.clistNode);
			this.clist.on('dgrid-refresh-complete', function(evt) {
				var empty_term = {id:0, term:"All Categories", slug:"all"};
				if (self.category.length == 0 || self.category[0].id == 0) empty_term.selected = true; 
				self.clist.renderArray([empty_term]);
			});
			this.clist.startup();

			//initialize the uploader
			this.uploader.url = WEBSITE_URL+'upload';	
			this.uploader.onBegin = lang.hitch(this, function() {
				self.set_status('loading');
			});
			this.uploader.onComplete = lang.hitch(this, 'onUpload');
			
			//attach mode buttons
			on(this.detailsMode, 'click', function() {
				self.setMode('details');
			});
			on(this.iconsMode, 'click', function() {
				self.setMode('icons');
			});
			
			//search
			on(this.search, 'keyup', function(evt) {
				if (evt.keyCode <= 31 && evt.keyCode != 8) return;
				self.query['keywords'] = self.search.value;
				if (self.mode == 'details') self.grid.set('query', self.query);
				else self.list.set('query', self.query); 
			});
			
			//query string params
			var parts = window.location.search.substr(1).split('&');
			for (var i in parts) {
				var value = parts[i].split('=');
				if (value[0] == 'mode') this.setMode(value[1]);
				else if (value[0] == 'modal') {
					this.modal = true;
					this.modalMenu.style.display = 'block';
				}
			}			
			
		},
		setMode: function(mode) {
			this.mode = mode;
			if (mode == 'details') {
				domclass.remove(this.iconsMode, 'active');
				domclass.add(this.detailsMode, 'active');
				this.listNode.style.display = 'none';
				this.gridNode.style.display = 'block';
				this.contextMenu.style.display = 'none';
				if (this.grid == null) {
					this.grid = new Grid({model:'files', action:'list', query:this.query, editor:this}, this.gridTableNode);
				} else this.grid.set('query', this.query);
			} else if (mode == 'icons') {
				domclass.remove(this.detailsMode, 'active');
				domclass.add(this.iconsMode, 'active');
				this.gridNode.style.display = 'none';
				this.listNode.style.display = 'block';
				this.list.set('query', this.query);
			}
		},
		delete: function() {
			var self = this;
			if (confirm('Are you sure you want to delete this image?')) {
				for (var id in self.files) {
					self.list.store.remove(id).then(function() {
						self.list.removeRow(self.files[id][1]);
						delete self.files[id];
						self.contextMenu.style.display = 'none';
					});
				}
			}
		},
		edit: function(id) {
			var self = this, callback = function(data){if (self.mode == 'details') self.grid.refresh(); else self.list.refresh();};
			if (!id) for (var i in self.files) id = i;
			sb.dialog('edit-file', {
				url:'admin/media/',
				callback:callback
			}, true).then(function(dialog) {
				dialog.show(id);
			});
		},
		select_category: function(term, node) {
			/**
			 * select a category from the sidebar
			 */
			//de-activate the previously active node
			if (this.category.length > 0) {
				domclass.remove(this.category[1], 'active');
			}
			//activate the new node
			domclass.add(node, 'active');
			//update the selection
			this.category = [term, node];
			//update the uploader
			this.uploader.category = (term.id == 0) ? '' : 'files_category '+term.slug;
			//update the query (filters the file list)
			this.query.category = term.id;
			if (this.mode == "icons") this.list.set('query', this.query);
			else if (this.mode == "details") this.grid.set('query', this.query);
		},
		new_category: function() {
			/**
			 * create a new category
			 */
			var self = this, callback = function(data) {self.clist.refresh();};
			sb.dialog('new-file-category', {
				url:'admin/taxonomies/create.xhr?taxonomy=files_category&',
				crudSuffixes:false,
				callback: callback
			});
		},
		set_status: function(value) {
			/**
			 * updates the status indicator
			 */
			if (!value) this.statusNode.innerHTML = '';
			else if (value == 'loading') {
				this.statusNode.innerHTML = '<img src="'+WEBSITE_URL+'app/themes/storm/public/images/loading.gif" width="70"/>';
			} else if (value == 'editing') {
				this.statusNode.innerHTML = '<br/><strong>Editing this comment...</strong>';
			} else this.statusNode.innerHTML = value;
		},
		onUpload: function(files) {
			/**
			 * upload handler. adds the file to the list once it has been uploaded.
			 */
			this.set_status();
			for (var i in files) files[i].filename = files[i].original_name;
			this.list.renderArray(files);
			if (this.grid != null) this.grid.renderArray(files);
		},
		close: function() {
			window.close();
		}
	});
});
