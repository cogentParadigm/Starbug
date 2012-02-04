var copyOnly = function(mid){
		var list = {
			"starbug/starbug.profile":1,
			"starbug/package.json":1
		};
		return (mid in list) || /^starbug(.*)templates\//.test(mid) || /^starbug(.*)css\//.test(mid);
	};

var profile = {
	resourceTags:{
		test: function(filename, mid){
			return false;
		},
		copyOnly: function(filename, mid){
			return copyOnly(mid);
		},
		amd: function(filename, mid){
			return true;
		}
	},

	trees:[
		[".", ".", /(\/\.)|(~$)/]
	]
};



