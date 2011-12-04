var sbamd = function(mid){
		var list = {
			"starbug/grid/EnhancedGrid":1,
			"starbug/data/ObjectStore":1
		};
		return (mid in list);
	};

var profile = {
	resourceTags:{
		test: function(filename, mid){
			return false;
		},

		amd: function(filename, mid){
			return true;
		}
	},

	trees:[
		[".", ".", /(\/\.)|(~$)/]
	]
};



