dependencies = {
	layers: [
		{
			// This is a specially named layer, literally 'dojo.js'
			// adding dependencies to this layer will include the modules
			// in addition to the standard dojo.js base APIs.
			name: "dojo.js",
			dependencies: [
				'dbootstrap',
				'sb',
				'dojo/selector/acme',
				'dojo/query',
				'bootstrap/Dropdown',
				'bootstrap/Carousel'
			]
		}
	],

	prefixes: [
		["dijit", "../dijit"],
		["dojox", "../dojox"],
		["dstore", "../dstore"],
		["dgrid", "../dgrid"],
		["put-selector", "../put-selector"],
		["xstyle", "../xstyle"],
		["dbootstrap", "../dbootstrap"],
		["bootstrap", "../bootstrap"],
		["sb", "../../../core/app/public/js/sb"],
		["starbug", "../../../core/app/public/js/starbug"],
		["app", "../../../app/public/js"]
	]
}
