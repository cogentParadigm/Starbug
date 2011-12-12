define(['dojo', 'sb'], function(dojo, sb) {
	sb.star = function(str) {
		var starr = {};
		var pos = null;
		var keypairs = str.split('  ');
		for (var i in keypairs) {
			i = keypairs[i];
			if (-1 != (pos = i.indexOf(':'))) starr[i.substr(0, pos)] = i.substr(pos+1);
		}
		return starr;
	};
	return sb.star;
});
