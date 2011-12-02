dojo.provide("starbug.list.List");
dojo.require("starbug.list.Item");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("starbug.data.ObjectStore");
dojo.declare('starbug.list.List', [dijit._Widget, dijit._Templated], {
	itemClass: starbug.list.Item,
	itemTemplate:'',
	store: null,
	query: '',
	model:'',
	models:'',
	headers:{},
	items:{},
	templateString: dojo.cache("starbug.list", "templates/List.html"),
	widgetsInTemplate: true,
	postCreate: function() {
		this.inherited(arguments);
		this.store = new starbug.data.ObjectStore({
			apiQuery: this.query,
			onItem: dojo.hitch(this, 'addItem'),
			onChange: dojo.hitch(this, 'updateItem')
		});
		this.model = this.store.model;
		this.models = this.store.models;
		this.store.fetch({onItem:dojo.hitch(this, 'addItem')});
		for (var i in this.headers) {
			dojo.create('li', {'id':this.id+'_'+i, 'class':i, 'innerHTML':this.headers[i].label}, this.header, 'last');
		}
	},
	addItem: function(item) {
		if (item.status == '1') return;
		var li = dojo.create('li', {id:this.id+'_item_'+item.id}, this.containerNode, 'last');
		this.items[item.id] = new this.itemClass({item:item, list:this}, li);
	},
	updateItem: function(item) {
		if (item.status == '1') this.items[item.id].destroy();
		else {
			this.items[item.id].item = item;
			this.items[item.id].render();
		}
	}
});
