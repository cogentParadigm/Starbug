define(["dojo/_base/declare", "dijit/_WidgetBase", "dojo/query", "dojo/NodeList-manipulate", "dojo/NodeList-traverse", "dojo/NodeList-dom", "dojo/NodeList-fx"], function(declare, Widget, query) {
	return declare([Widget], {
		accordion:true,
		speed:235,
		closedSign:'<em class="fa fa-plus-square-o"></em>',
		openedSign:'<em class="fa fa-minus-square-o"></em>',
		postCreate: function() {
			var self = this;
			//add a mark [+] to a multilevel menu
			query('li', this.domNode).forEach(function(li) {
				if (query('ul', li).length !== 0) {
					var link = query('> a', li);
					link.append("<b class='collapse-sign'>"+self.closedSign+"</b>");
				}
			});
			//open active level
			query("li.active", this.domNode).forEach(function(li) {
				var parents = query(li).parents("ul");
				parents.wipeIn({duration:self.speed, beforeBegin: function() {this.node.style.display = 'block';}}).play();
				parents.parent("li").query("b").first().html(self.openedSign);
				parents.parent("li").addClass("open");
			});
			//attach click event
			query("li a", this.domNode).on('click', function() {
				var parent = query(this).parent();
				if (parent.query("ul").length !== 0) {
					if (self.accordion) {
						//Do nothing when the list is open
						if (parent.query("ul").style('display')[0] != 'block') {
							var parents = parent.parents("ul");
							visible = self.getVisibleMenus();
							visible.forEach(function(visibleNode) {
								var close = true;
								parents.forEach(function(parentNode) {
									if (parentNode == visibleNode) {
										close = false;
										return false;
									}
								});
								if (close) {
									if (parent.query("ul")[0] != visibleNode) {
										query(visibleNode).wipeOut({duration: self.speed, onEnd: function(node) {
											this.node.style.display = 'none';
											query(node).parent("li").query("b").first().html(self.closedSign);
											query(node).parent("li").removeClass("open");
										}}).play();
									}
								}
							});
						}
					}
					if (parent.query("ul").first().style("display")[0] == "block" && parent.query("ul").first()[0].className != "active") {
						parent.query("ul").first().wipeOut({duration:self.speed, onEnd:function(node) {
							query(node).parent("li").removeClass("open");
							query(node).parent("li").query("b").first().html(self.closedSign);
						}}).play();
					} else {
						parent.query("ul").first().style('display', 'none').wipeIn({duration:self.speed, onEnd:function(node) {
							query(node).parent("li").addClass("open");
							query(node).parent("li").query("b").first().html(self.openedSign);
						}}).play();
					}
				}
			});
		},
		getVisibleMenus: function() {
			var visible = query.NodeList();
			query('ul', this.domNode).forEach(function(ul) {
				if (ul.offsetHeight > 0 && ul.offsetWidth > 0) visible.push(ul);
			});
			return visible;
		}
	});
});
