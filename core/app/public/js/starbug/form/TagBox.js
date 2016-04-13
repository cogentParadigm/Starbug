define([
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dijit/_WidgetBase",
	"dijit/_TemplatedMixin",
	"dijit/_WidgetsInTemplateMixin",
	"sb/store/Api",
	"dojo/text!./templates/TagBox.html",
	"dgrid/OnDemandList",
	"put-selector/put",
	"dojo/on",
	"dojo/date/locale",
	"dojo/dom",
	"dojo/dom-attr",
	"dojo/dom-class",
	"dijit/form/TextBox",
	"dijit/form/Button"
], function (declare, lang, Widget, Templated, _WidgetsInTemplate, Api, template, List, put, on, locale, dom, domattr, domclass) {
	return declare([Widget, Templated, _WidgetsInTemplate], {
		currentUser:0, //logged in user
		readOnly:false,
		store:null,
		query:{}, //parameters for the comments (eg. {request:1950} or {task:2730})
		mode:'update',
		listNode:null, //attached in the template, the node for the dgrid OnDemandList
		list:null, //the dgrid OnDemandList that holds the list of comments
		editor:null, //attached in the template, the textarea where comments are entered
		templateString: template, //the template (./templates/TagBox.html)
		widgetsInTemplate: true,
		value:[],
		removeValues:[],
		input_name:'tags',
		postCreate:function() {
			var self = this;
			this.store = new Api({model:'terms', action:'tags'});

			this.input.name = this.input_name;
			//instantiate a dgrid on demand list
			this.list = new List({
				store: this.store.filter(this.query),
				mode: this.mode,
				keepScrollPosition:true,
				renderRow: function(tag, options){
					//the renderRow function will render our list item
					//and attach events within the item.
					//We can use the scope of this function to access
					//the target node from within the event handlers

					//first put in a root node
					var node = put("div.tag");

					//delete
					on(put(node, 'a.right[href="javascript:;"]', put('div.fa.fa-times')), 'click', function() {
							var terms = self.value;
							for(var t in terms) {
								if(self.value[t] == tag) {
									self.value.splice(t, 1);
									self.removeValues.push(tag);
								}
							}
							self.list.removeRow(node);
							self.updateValue();
					});
					// put tag into the node
					put(node, 'div.term', tag);

					//return the node
					return node;
				}
			}, this.listNode);
			this.list.startup();
			if (this.value.length > 0) {
				values = [];
				for (var v in this.value) {
					var val = this.value[v].trim();
					if (val != "") values.push(val);
				}
				this.value = values;
				if(this.value.length > 0) this.apply();
			}

		},
		apply:function(){
			//the newItem function is attached to the 'Apply' button.
			//it also handles saving edits
			var self = this;
			var tags = this.editor.get('value').split(/,/g);
			var terms = this.value;
			for (var t in tags) {
				tags[t] = tags[t].trim();
				var duplicate = false;
				for (var i in terms) {
					if(this.value[i] == tags[t]) duplicate = true;
				}
				for (var i in this.removeValues) {
					if (this.removeValues[i] == tags[t]) this.removeValues.splice(i, 1);
				}
				if (!duplicate && tags[t] != "") this.value.push(tags[t]);
			}
			self.updateValue();
			self.editor.set('value', '');
		},
		updateValue:function() {
			var values = [];
			for (var i in this.value) values.push(this.value[i]);
			for (var i in this.removeValues) values.push("-"+this.removeValues[i]);
			domattr.set(this.input, 'value', values.join(','));
			this.list.renderArray(this.value);
		},
		set_status: function(value) {
			if (!value) this.statusNode.innerHTML = '';
			else if (value == 'loading') {
				this.statusNode.innerHTML = '<img src="'+WEBSITE_URL+'app/themes/storm/public/images/loading.gif" width="70"/>';
			} else if (value == 'editing') {
				this.statusNode.innerHTML = '<br/><strong>Editing this comment...</strong>';
			} else this.statusNode.innerHTML = value;
		}
	});
});
