dependencies = {
	layers: [
		{
			// This is a specially named layer, literally 'dojo.js'
			// adding dependencies to this layer will include the modules
			// in addition to the standard dojo.js base APIs. 
			name: "dojo.js",
			dependencies: [
				'sb',
				'sb/data'
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
		["jquery", "//ajax.googleapis.com/ajax/libs/jquery/1.9.1"],
		["sb", "../../sb"],
		["starbug", "../../starbug"],
		["app", "../../../../../../app/public/js"]
	]
}
