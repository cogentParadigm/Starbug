define(["dojo", "dojo/on", "dojo/parser", "dojo/ready", "dijit/_WidgetBase"], function(dojo, on, parser, ready, _WidgetBase) {
	return dojo.declare("starbug.form.StateSelect", [_WidgetBase], {
		// summary:
		//		Behavioral widget to toggle visible state/province options in a select element.
		//		Populate the select with all of the states/provinces and a data-country attribute that matches the country value.
		
		// regions: node
		//		an internal node to hold inactive states/provinces
		regions:null,
		
		// country_select: string/node
		//		the country select node or id
		country_select:null,

		// selected_country: string
		//		the currently selected country
		selected_country:'US',

		// text_input: node
		//		a text input for 
		text_input:null,

		postCreate: function(args) {
			//initialize regions to a hidden select
			this.regions = dojo.create('select', {'style':{'display':'none'}});
			
			//if country_select is a string convert it to a node
			if (typeof this.country_select == 'string') this.country_select = dojo.byId(this.country_select);
			
			//initialize the text field
			this.text_input = dojo.create('input', {
				'name':dojo.attr(this.domNode, 'name'),
				'value':dojo.attr(this.domNode, 'data-value'),
				'class':'text',
				'disabled':'disabled',
				'style':{'display':'none'}
			}, this.domNode, 'after');
			
			//attach to the onchange event of the country select
			on(this.country_select, 'change', dojo.hitch(this, 'update'));
			
			//update to match the initial country
			this.update();
		},
		update: function() {
			//get the selected country
			this.selected_country = dojo.attr(this.country_select, 'value');
			//remove foreign regions
			dojo.query('option:not([data-country=\"'+this.selected_country+'\"])', this.domNode).place(this.regions, "last");
			//add local regions
			dojo.query('option[data-country=\"'+this.selected_country+'\"]', this.regions).place(this.domNode, "last");
			//toggle select/input
			if (dojo.query('option', this.domNode).length > 0) {
				this.disable(this.text_input);
				this.enable(this.domNode);
			} else {
				this.disable(this.domNode);
				this.enable(this.text_input);				
			}
		},
		enable: function(node) {
				dojo.style(node, 'display', 'block');
				dojo.removeAttr(node, 'disabled');
		},
		disable: function(node) {
				dojo.style(node, 'display', 'none');
				dojo.attr(node, 'disabled', 'disabled');			
		}
	});
});
