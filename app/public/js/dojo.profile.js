dependencies = {
	layers: [
		{
			// This is a specially named layer, literally 'dojo.js'
			// adding dependencies to this layer will include the modules
			// in addition to the standard dojo.js base APIs. 
			name: "dojo.js",
			dependencies: [
				'dojo.behavior',
				'dijit.Dialog'
			]
		}
	],

	prefixes: [
		["dijit", "../dijit" ],
		["dojox", "../dojox" ],
		["starbug", "../../../../../core/app/public/js/dojo/starbug"]
	]
}