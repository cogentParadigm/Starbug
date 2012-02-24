define(['dojo', 'sb/kernel'], function(dojo, sb) {
	var strings = {
		star: function(str) {
			if (typeof str != "string") return str;
			var starr = {};
			var pos = null;
			var keypairs = str.split('  ');
			for (var i in keypairs) {
				i = keypairs[i];
				if (-1 != (pos = i.indexOf(':'))) starr[i.substr(0, pos)] = i.substr(pos+1);
			}
			return starr;
		},
		normalize: function(string, pattern) {
			pattern = pattern || /[^a-zA-Z0-9\-]/g;
			return string.replace(pattern, '');
		},
		html_attributes: function(args) {
			args = this.star(args);
			var output = '';
			for (var i in args) {
				if (typeof args[i] == "string") output += ' '+i+'="'+args[i]+'"';
			}
			return output;
		}
	};
	return strings;
});
