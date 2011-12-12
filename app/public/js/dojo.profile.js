dependencies = {
	layers: [
		{
			// This is a specially named layer, literally 'dojo.js'
			// adding dependencies to this layer will include the modules
			// in addition to the standard dojo.js base APIs. 
			name: "dojo.js",
			dependencies: [
				'dojo.behavior',
				'dijit.Dialog',
				'sb'
			]
		},
		{
			name: "../starbug/ide.js",
			dependencies: [
				'starbug.IDE.IDE'
			]
		}
	],

	prefixes: [
		["dijit", "../dijit" ],
		["dojox", "../dojox" ],
		["sb", "../../../../../core/app/public/js/dojo/sb"],
		["starbug", "../../../../../core/app/public/js/dojo/starbug"]
	]
}
