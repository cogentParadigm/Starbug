define(['dojo', 'sb/kernel', 'dojo/date/locale'], function(dojo, sb, locale) {
	dojo.global.sb.strings = {
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
		},
		nl2br: function(data) {
			return data.replace(/\n/g, '<br />\n');
		},
		date: function(date) {
			if (typeof date == "string") {
				if (date == '0000-00-00 00:00:00') return '';
				var t = date.split(/[- :]/);
				date = new Date(t[0], parseInt(t[1],10)-1, t[2], t[3], t[4], t[5]);
			}
			return locale.format(date, {datePattern: "MMM d, y", selector: "date"});  //"Nov 6, 2001"
		},
		htmlentities: function(str){
		 var aStr = str.split(''),
				 i = aStr.length,
				 aRet = [];

			 while (--i >= 0) {
				var iC = aStr[i].charCodeAt();
				if (iC < 65 || iC > 127 || (iC>90 && iC<97)) {
					aRet.push('&#'+iC+';');
				} else {
					aRet.push(aStr[i]);
				}
			}
		 return aRet.reverse().join('');
		}
	};
	return dojo.global.sb.strings;
});
