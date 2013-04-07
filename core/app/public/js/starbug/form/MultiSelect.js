define([
	"dojo/_base/array", // array.indexOf, array.map
	"dojo/_base/declare", // declare
	"dojo/dom-geometry", // domGeometry.setMarginBox
	"dojo/has",
	"dojo/_base/lang",
	"dojo/query", // query
	"dojo/on",
	"dijit/form/_FormSelectWidget",
	"put-selector/put"
], function(array, declare, domGeometry, has, lang, query, on, _FormSelectWidget, put){

	// module:
	//		starbug/form/MultiSelect

	var MultiSelect = declare(_FormSelectWidget, {
		// summary:
		//		Multiple select widget,
		//		for selecting multiple options.
		multiple:true,
		mode:'bitwise',
		
		templateString: '<div class="multiple_select" data-dojo-attach-point="containerNode, focusNode"></div>',
		
		labelAttr:'label',

		// emptyLabel: string
		//		What to display in an "empty" dropdown
		emptyLabel: "&#160;", // &nbsp;

		// _isLoaded: Boolean
		//		Whether or not we have been loaded
		_isLoaded: false,

		// _childrenLoaded: Boolean
		//		Whether or not our children have been loaded
		_childrenLoaded: false,
		
		_loadChildren: function() {
			window.ms = this;
			query('.multiple_select_item', this.containerNode).forEach(function(item){item.parentNode.removeChild(item);});
			this.inherited(arguments);
		},
		
		_getNodeForOption: function(option){
			// summary:
			//		For the given option, return the node that should be
			//		used to display it.  This can be overridden as needed
				// Just a regular menu option
				var opstr = (option.selected) ? '[checked=checked]' : '';
				var lbl = option.label || this.emptyLabel;
				var item = put('div.multiple_select_item');
				var input = put(item, 'input.left[type=checkbox][value='+option.value+'][data-label='+lbl+']'+opstr);
				put(item, 'label', lbl);
				on(input, 'change', lang.hitch(this, function(evt) {
					if (input.checked) this.updateOption({value:option.value, selected:true});
					else this.updateOption({value:option.value, selected:false});
				}));
				return item;
		},

		_addOptionItem: function(option){
			// summary:
			//		For the given option, add an option to our container.
			//		If the option doesn't have a value, then a separator is added
			//		in that place.
			this.containerNode.appendChild(this._getNodeForOption(option));
		},

		_setValueAttr: function(values, priorityChange){
			// summary:
			//		Hook so set('value', values) works.
			// description:
			//		Set the value(s) of this Select based on passed values
			var mode = this.mode, newValues = [];
			if (mode == "csv" && !lang.isArray(values)) values = values.split(/,/g);
			query("input", this.containerNode).forEach(function(n){
				if (lang.isArray(values)) {
					n.checked = (array.indexOf(values, n.value) != -1);
				} else if (mode == "bitwise") {
					n.checked = (parseInt(values) & parseInt(n.value));
					if (n.checked) newValues.push(n.value);
				}
			});
			if (mode == "bitwise") arguments[0] = newValues;
			this.inherited(arguments);
		},
		
		_getValueAttr: function() {
			if (this.mode == "bitwise") {
				return this.value.reduce(function(a, b) { return a + b; }, 0);
			} else if (this.mode == "csv") {
				return this.value.join(',');
			}
		},


		_onChange: function(/*Event*/){
			this._handleOnChange(this.get('value'), true);
		},

		postCreate: function(){
			//this._set('value', this.get('value'));
			this.inherited(arguments);
		}

	});

	return MultiSelect;
});
