define(["sb/marked"], function (marked) {
	marked.setOptions({
		breaks:true, smartLists:true,
		highlight:function(code) {
			if (typeof prettyPrintOne == "undefined") return code;
			var hint = code.split('\n')[0].trim();
			console.log(hint.substr(3).toLowerCase());
			if (hint.substr(0, 3) == '///') return prettyPrintOne(code, hint.substr(3).toLowerCase());
			else return prettyPrintOne(code);
		}
	});
	return marked;
});
