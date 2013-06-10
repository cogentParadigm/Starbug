dependencies = {
	layers: [
		{
			// This is a specially named layer, literally 'dojo.js'
			// adding dependencies to this layer will include the modules
			// in addition to the standard dojo.js base APIs. 
			name: "dojo.js",
			dependencies: [
				'sb',
				'dojo/selector/acme',
				'dojo/query',
				'bootstrap/Dropdown'
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
		["dijit", "../dijit"],
		["dojox", "../dojox"],
		["dgrid", "../dgrid"],
		["put-selector", "../put-selector"],
		["xstyle", "../xstyle"],
		["bootstrap", "../bootstrap"],
		["sb", "../../sb"],
		["starbug", "../../starbug"],
		["app", "../../../../../../app/public/js"]
	]
}
