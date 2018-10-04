var copyOnly = function(mid){
		var list = {
			"sb/sb.profile":1,
			"sb/package.json":1,
			"sb/marked":1
		};
		return (mid in list) || /^sb(.*)templates\//.test(mid) || /^sb(.*)css\//.test(mid);
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



