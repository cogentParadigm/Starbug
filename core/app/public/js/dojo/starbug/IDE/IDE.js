dojo.provide("starbug.IDE.IDE");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.layout.BorderContainer");
dojo.require("dijit.layout.AccordionContainer");
dojo.require("dijit.layout.TabContainer");
dojo.require("dijit.layout.ContentPane");
dojo.declare('starbug.IDE.IDE', [dijit._Widget, dijit._Templated], {
	tabs: [],
	positions: {},
	selectedTab: 0,
	openURL: '',
	saveURL: '',
	browseURL: '',
	errorURL: '',
	gitURL: '',
	startDir: '.',
	files: '[]',
	timers: [],
	templateString: dojo.cache("starbug", "templates/IDE.html"),
	widgetsInTemplate: true,
	postCreate: function() {
		this.inherited(arguments);
		this.browseTo(this.startDir);
		this.git('status');
		this.files = dojo.fromJson(this.files);
		if (this.files.length > 0) {
			for (var i in this.files) this.openFile(this.files[i], this.files[i].split('/').pop());
		}
		dojo.subscribe("editor-selectChild", this, 'onTabSelected');
		dojo.subscribe("editor-removeChild", this, 'closeFile');
	},
	onTabSelected: function(tab) {
		this.selectedTab = parseInt(tab.id.substr(tab.id.length-1, 1));
	},
	browseTo: function(loc) {
		dojo.xhrGet({
			url: this.browseURL,
			content: {browse: loc},
			load: dojo.hitch(this, function(data) {
				this.browser.set('content', data);
			})
		});
	},
	git: function(cmd) {
		dojo.xhrGet({
			url: this.gitURL,
			content: {command: cmd},
			handleAs: 'json',
			load: dojo.hitch(this, function(data) {
				var output = '<strong>Branch:</strong> <strong style="color:blue">'+data.branch+'</strong><br/><br/>';
				if (data.staged.length > 0) {
					output += '<strong>staged:</strong><br/>';
					for (var i in data.staged) {
						output += '<span style="color:green"> '+data.staged[i]+'</span><br/>';
					}
				}
				if (data.modified.length > 0) {
					output += '<br/><strong>modified:</strong><br/>';
					for (var i in data.modified) {
						output += '<div style="color:red"><a class="right" style="margin-right:5px">add</a> '+data.modified[i]+'</div>';
					}
				}
				if (data.untracked.length > 0) {
					output += '<br/><strong>untracked:</strong><br/>';
					for (var i in data.untracked) {
						output += '<div style="color:red"><a class="right" style="margin-right:5px">add</a> '+data.untracked[i]+'</div>';
					}
				}
				this.gitbox.set('content', output);
			})
		});
	},
	openFile: function(loc, caption) {
		if (this.positions[loc] != null) {
			this.editor.selectChild(this.tabs[this.positions[loc]].pane);
		} else {
			dojo.xhrGet({
				url: this.openURL,
				content: {id: 'tab'+this.tabs.length, open: loc},
				load: dojo.hitch(this, function(data) {
					var tab = new dijit.layout.ContentPane({
						title: caption,
						content: data,
						closable: true
					});
					this.editor.addChild(tab);
					var cm = CodeMirror.fromTextArea('tab'+this.tabs.length, {
						height: "dynamic",
						parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js", "../contrib/php/js/parsephphtmlmixed.js"],
						stylesheet: ["app/public/js/CodeMirror/css/xmlcolors.css", "app/public/js/CodeMirror/css/jscolors.css", "app/public/js/CodeMirror/css/csscolors.css", "app/public/js/CodeMirror/contrib/php/css/phpcolors.css"],
						path: "app/public/js/CodeMirror/js/",
						tabMode: "shift",
						lineNumbers: "true",
						autoMatchParens: true,
						saveFunction: dojo.hitch(this, 'saveFile'),
						onChange: dojo.hitch(this, 'dirtySelected'),
						textWrapping: false 
					});
					this.positions[loc] = this.tabs.length;
					this.tabs.push({pane: tab, editor: cm, content: cm.getCode(), path: loc, file: caption, dirty: false});
					this.setType(this.tabs.length-1);
					this.editor.selectChild(tab);
				})
			});
		}
	},
	saveFile: function() {
		var current = this.tabs[this.selectedTab];
		dojo.xhrPost({
			url: this.saveURL,
			content: {
				open: this.tabs[this.selectedTab].path,
				old: this.tabs[this.selectedTab].content,
				new: this.tabs[this.selectedTab].editor.getCode()
			},
			handleAs: 'json',
			load: dojo.hitch(this, function(data) {
				if (data.status == 'saved') {
					this.tabs[this.selectedTab].content = this.tabs[this.selectedTab].editor.getCode();
					this.cleanSelected();
					this.git('status');
				} else if (data.status == 'changed on disk') {
					alert('The file has changed on disk.');
				}
			})
		});
	},
	closeFile: function(tab) {
		this.positions[this.tabs[parseInt(tab.id.substr(tab.id.length-1, 1))].path] = null;
	},
	setType: function(idx) {
		var sel = dojo.byId('tab'+idx+'_type');
		this.tabs[idx].editor.setParser(sel.options[sel.selectedIndex].value);
	},
	dirtySelected: function() {
		if (this.timers[this.selectedTab] != null) clearTimeout(this.timers[this.selectedTab]);
		setTimeout(dojo.hitch(this, 'checkErrors', this.selectedTab), 2000);
		if (!this.tabs[this.selectedTab].dirty) {
			this.tabs[this.selectedTab].dirty = true;
			this.tabs[this.selectedTab].pane.set('title', '<span style="color:#C00;font-weight:bold">*</span>'+this.tabs[this.selectedTab].file);
		}
	},
	cleanSelected: function() {
		this.tabs[this.selectedTab].dirty = false;
		this.tabs[this.selectedTab].pane.set('title', this.tabs[this.selectedTab].file);
	},
	checkErrors: function(idx) {
		dojo.xhrPost({
			url: this.errorURL,
			content: {content: this.tabs[idx].editor.getCode(), type: dojo.attr('tab'+idx, 'type')},
			load: dojo.hitch(this, function(data) {
				dojo.query('.alerts', this.tabs[idx].pane.domNode)[0].innerHTML = data;
			})
		});
	}
});