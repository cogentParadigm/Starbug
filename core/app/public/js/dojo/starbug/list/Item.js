dojo.provide("starbug.list.Item");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dojo.date.locale");
dojo.declare('starbug.list.Item', [dijit._Widget, dijit._Templated], {
	list: null,
	item: null,
	templateString: dojo.cache("starbug.list", "templates/Item.html"),
	widgetsInTemplate: true,
	postCreate: function() {
		this.inherited(arguments);
		this.render();
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
		var content = this.list.itemTemplate;
		for (var i in this.item) content = content.replace('%'+i+'%', this.item[i]);
		dojo.attr(this.body, 'innerHTML', content);
	}
});
