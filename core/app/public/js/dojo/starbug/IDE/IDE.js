define([
	"dojo",
	"dijit",
	"dijit/_Widget",
	"dijit/_Templated",
	"dojo/text!../templates/IDE.html",
	"dijit/layout/BorderContainer",
	"dijit/layout/AccordionContainer",
	"dijit/layout/TabContainer",
	"dijit/layout/ContentPane",
	"dijit/Dialog",
	"dijit/form/ComboBox",
	"dijit/form/Button"
], function(dojo, dijit, _Widget, _Templated, template) {
var IDE = dojo.declare('starbug.IDE.IDE', [_Widget, _Templated], {
	tabs: [],
	positions: {},
	selectedTab: 0,
	rogueURL: '',
	openURL: '',
	saveURL: '',
	browseURL: '',
	errorURL: '',
	gitURL: '',
	startDir: '.',
	lastUpdate:'',
	files: '[]',
	timers: [],
	dialog: null,
	branches:null,
	info:null,
	consoleTimer:null,
	templateString: template,
	widgetsInTemplate: true,
	postCreate: function() {
		this.inherited(arguments);
		this.lastUpdate = dojo.config.serverTime;
		this.dialog = new dijit.Dialog();
		this.browseTo(this.startDir);
		this.git('status');
		this.files = dojo.fromJson(this.files);
		if (this.files.length > 0) {
			for (var i in this.files) this.openFile(this.files[i], this.files[i].split('/').pop());
		}
		dojo.subscribe("editor-selectChild", this, 'onTabSelected');
		dojo.subscribe("editor-removeChild", this, 'closeFile');
		dojo.connect(window, "onkeypress", dojo.hitch(this, function(e) {
			if ((e.ctrlKey && e.charCode == 115) || (e.charCode == 19)) {
				e.preventDefault();
				this.saveFile();		
			}
		}));
		this.consoleTimer = setInterval(dojo.hitch(this, 'updateConsole'), 4000);
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
	updateConsole: function() {
		dojo.xhrGet({
			url: this.rogueURL+'/console',
			content: {time: this.lastUpdate},
			handleAs:'json',
			load: dojo.hitch(this, function(data) {
				this.lastUpdate = data.time;
				data.html = this.console.get('content') + data.html;
				this.console.set('content', data.html);
			})
		});
	},
	/**
	 * open a file for editing
	 * @param string loc the file path
	 * @param string caption the tab title
	 */
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
					var cm = CodeMirror.fromTextArea(document.getElementById('tab'+this.tabs.length), {
						height: "dynamic",
						mode: "htmlmixed",
						tabMode: "shift",
						lineNumbers: "true",
						autoMatchParens: true,
						saveFunction: dojo.hitch(this, 'saveFile'),
						onChange: dojo.hitch(this, 'dirtySelected'),
						textWrapping: false
					});
					dojo.connect(document.getElementById('tab'+this.tabs.length), 'onchange', dojo.hitch(this, 'saveFile'));
					this.positions[loc] = this.tabs.length;
					this.tabs.push({pane: tab, editor: cm, content: document.getElementById('tab'+this.tabs.length).innerText, path: loc, file: caption, dirty: false});
					this.setType(this.tabs.length-1);
					this.editor.selectChild(tab);
				})
			});
		}
	},
	_onEditorTab: function(editor) {
		this.tabs[this.tabs.length-1].content = this.editor.getValue();
	},
	/**
	 * save the file in the currently selected tab
	 */
	saveFile: function() {
		var current = this.tabs[this.selectedTab];
		dojo.xhrPost({
			url: this.saveURL,
			content: {
				'open': this.tabs[this.selectedTab].path,
				'old': this.tabs[this.selectedTab].content,
				'new': this.tabs[this.selectedTab].editor.getValue()
			},
			handleAs: 'json',
			load: dojo.hitch(this, function(data) {
				if (data.status == 'saved') {
					this.tabs[this.selectedTab].content = this.tabs[this.selectedTab].editor.getValue();
					this.cleanSelected();
					this.git('status');
				} else if (data.status == 'changed on disk') {
					alert('The file has changed on disk.');
				}
			})
		});
	},
	/**
	 * close a tab
	 * @param object tab the tab to close
	 */
	closeFile: function(tab) {
		this.positions[this.tabs[parseInt(tab.id.substr(tab.id.length-1, 1))].path] = null;
	},
	/**
	 * set the editors highlighting type
	 */
	setType: function(idx) {
		this.tabs[idx].editor.setOption('mode', dojo.attr('tab'+idx+'_type', 'value'));
	},
	/**
	 * marks the selected tab as 'dirty' (unsaved)
	 */
	dirtySelected: function() {
		if (this.timers[this.selectedTab] != null) clearTimeout(this.timers[this.selectedTab]);
		setTimeout(dojo.hitch(this, 'checkErrors', this.selectedTab), 1000);
		if (!this.tabs[this.selectedTab].dirty) {
			this.tabs[this.selectedTab].dirty = true;
			this.tabs[this.selectedTab].pane.set('title', '<span style="color:#C00;font-weight:bold">*</span>'+this.tabs[this.selectedTab].file);
		}
	},
	/**
	 * removes 'dirty' markers from the selected tab
	 */
	cleanSelected: function() {
		this.tabs[this.selectedTab].dirty = false;
		this.tabs[this.selectedTab].pane.set('title', this.tabs[this.selectedTab].file);
	},
	/**
	 * submit a tabs contents for error checking
	 * @param int idx the index of the tab
	 */
	checkErrors: function(idx) {
		dojo.xhrPost({
			url: this.errorURL,
			content: {'content': this.tabs[idx].editor.getValue(), 'type': dojo.attr('tab'+idx, 'type')},
			load: dojo.hitch(this, function(data) {
				dojo.query('.alerts', this.tabs[idx].pane.domNode)[0].innerHTML = data;
			})
		});
	},
	/**
	 * excutes a git command and update the status
	 * @param string cmd the git command to execute
	 */
	git: function(cmd) {
		dojo.xhrGet({
			url: this.gitURL,
			content: {command: cmd},
			handleAs: 'json',
			load: dojo.hitch(this, function(data) {
				this.info = data;
				if (this.branches == null) {
					this.branches = new dojo.data.ItemFileReadStore({
						data: {
							identifier: 'name',
							items: data.branches
						}
					});
					this.branches.clearOnClose = true;
				} else {
					this.branches.data = {identifier:'name', items:data.branches};
					this.branches.close();
				}
				var output = '<a class="right" style="margin-right:5px" href="javascript:dijit.byId(\''+this.id+'\').checkout()">switch</a><strong>Branch:</strong> <strong style="color:blue">'+data.branch+'</strong><br/><br/>';
				if (data.staged.length > 0) {
					output += '<strong>staged:</strong><br/>';
					for (var i in data.staged) {
						output += '<span style="color:green"><a class="right" style="margin-right:5px" href="javascript:dijit.byId(\''+this.id+'\').git(\'reset HEAD '+data.modified[i]+'\')">unstage</a> '+data.staged[i]+'</span><br/>';
					}
				}
				if (data.modified.length > 0) {
					output += '<br/><strong>modified:</strong><br/>';
					for (var i in data.modified) {
						output += '<div style="color:red"><a class="right" style="margin-right:5px" href="javascript:dijit.byId(\''+this.id+'\').git(\'add '+data.modified[i]+'\')">add</a> '+data.modified[i]+'</div>';
					}
				}
				if (data.untracked.length > 0) {
					output += '<br/><strong>untracked:</strong><br/>';
					for (var i in data.untracked) {
						output += '<div style="color:red"><a class="right" style="margin-right:5px" href="javascript:dijit.byId(\''+this.id+'\').git(\'add '+data.untracked[i]+'\')">add</a> '+data.untracked[i]+'</div>';
					}
				}
				if ((data.modified.length > 0) || (data.staged.length > 0)) {
					output += '<br/>';
				}
				if (data.staged.length > 0) {
					output += '<div style="color:green"><a href="javascript:dijit.byId(\''+this.id+'\').commit()">commit</a></div>';
				}
				if (data.modified.length > 0) {
					output += '<div style="color:green"><a href="javascript:dijit.byId(\''+this.id+'\').commit(true)">commit all</a></div>';
				}
				if (data.diff > 0) {
					output += '<br/>ahead by '+data.diff+' commit(s)<br/><a href="javascript:dijit.byId(\''+this.id+'\').push()">push</a>';
				}
				if (data.output != '') {
					output += '<br/><br/><div class="console_output">'+data.output+'</div>';
				}
				this.gitbox.set('content', output);
			})
		});
	},
	checkout: function() {
		this.dialog.set('title', 'Change Branch');
		this.dialog.set('content', '<form id="switch_branch_form"><div class="field"><label>Branch</label><div id="branch_list"></div><button id="switch_branch" type="button">Checkout Branch</button></div></form>');
		dojo.style(this.dialog.domNode, 'width', '200px');
		var sel = new dijit.form.ComboBox({
			id:'branch_list',
			name:'branch',
			value:this.info.branch,
			store:this.branches,
			searchAttr:'name'
		}, 'branch_list');
		new dijit.form.Button({
			label:'Checkout Branch',
			onClick:dojo.hitch(this, function() {
				this.git('checkout '+sel.get('value'));
			})
		}, 'switch_branch');
		this.dialog.show();
	},
	commit: function(all) {
		this.dialog.set('title', 'Commit Changes');
		this.dialog.set('content', '<form id="commit_form"><label>Message</label><textarea id="commit_msg" style="width:250px;height:70px"></textarea><br class="clear"/><button id="commit_btn" type="button"></button></form>');
		dojo.style(this.dialog.domNode, 'width', '270px');
		new dijit.form.Button({
			label:'Commit Changes',
			onClick: dojo.hitch(this, function() {
				if (all) flags = '-am';
				else flags = '-m';
				this.git('commit '+flags+' "'+dojo.attr('commit_msg', 'innerText')+'"');
			})
		}, 'commit_btn');
		this.dialog.show();
	},
	push: function() {
		this.git('push');
	}
});
return IDE;
});
