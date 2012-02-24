define(['dojo', 'require', 'sb/kernel'], function(dojo, require, sb) {

	var getTemplate = function(template, sync, load){
		sb.xhr(WEBSITE_URL, {method:"post", content:{'render':template}, sync:!!sync, action:load});
	};

	var
		theCache= {},
		notFound = {},
		pending = {},
		result= {
			dynamic:
				// the dojo/text caches it's own resources because of dojo.cache
				true,

			load:function(id, require, load){
				// id is something like (path is always absolute):
				//
				//	 "path/to/text.html"
				//	 "path/to/text.html!strip"
				var
					parts= id.split("!"),
					template = parts[0],
					text = notFound,
					finish = function(text){
						load(text);
					};
				if (template in theCache) text = theCache[template];
				if(text===notFound){
					if(pending[template]){
						pending[template].push(finish);
					}else{
						var pendingList = pending[template] = [finish];
						getTemplate(template, !require.async, function(text){
							theCache[template] = text;
							for (var i = 0; i<pendingList.length;) pendingList[i++](text);
							delete pending[template];
						});
					}
				}else{
					finish(text);
				}
			}
		};

	sb.render = function(template, val) {
		if (typeof val == "string") {
			//We have a string, set cache value
			theCache[template] = val;
			return val;
		} else if (val === null) {
			//Remove cached value
			delete theCache[template];
			return null;
		} else {
			//Allow cache values to be empty strings. If key property does
			//not exist, fetch it.
			if (!(template in theCache)) {
				getTemplate(WEBSITE_URL, true, function(text){
					theCache[template]= text;
				});
			}
			return theCache[template];
		}
	};
	sb.form = function(form, args, path) {
		return this.render(form+'&scope=forms', args, path);
	};
	return result;
});
