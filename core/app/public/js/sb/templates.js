define(['dojo', 'sb/kernel'], function(dojo, sb) {
	sb.render = function(template, args, path) {
		var t;
		var load = function(response, args, xhr) {
			t = response;
		};
		var url = (path) ? WEBSITE_URL+path : document.location;
		this.xhr({url:url+'?template='+template, method:'post', content:args, sync:true, action:load});
		return t;
	};
	sb.form = function(form, args, path) {
		return this.render(form+'&scope=forms', args, path);
	};
	return sb;
});
