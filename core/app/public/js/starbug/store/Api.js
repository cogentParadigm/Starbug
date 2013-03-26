define([
	"dojo",
	"dojo/store/util/QueryResults"
], function(dojo, QueryResults) {
return dojo.declare("starbug.store.Api", null, {
	// query: String
	//		The API query string
	apiQuery: "",
	// idProperty: String
	//		Indicates the property to use as the identity property. The values of this
	//		property should be unique.
	model: '',
	action:'admin',
	post_action: 'create',
	idProperty: "id",
	last_query:'',
	params:{},
	constructor: function(/*starbug.store.Api*/ options){
		// summary:
		//		This is a basic store for RESTful communicating with a server through JSON
		//		formatted data.
		// options:
		//		This provides any configuration information that will be mixed into the store
		dojo.mixin(this, options);
	},
	get: function(id, options){
		//	summary:
		//		Retrieves an object by its identity. This will trigger a GET request to the server using
		//		the url `this.target + id`.
		//	id: Number
		//		The identity to use to lookup the object
		//	returns: Object
		//		The object in the store that matches the given id.
		var args = sb.star(this.apiQuery);
		var parts = [];
		if (typeof args['where'] == 'undefined') args['where'] = this.model+'.id='+id;
		else args['where'] += ' && '+this.model+'.id='+id;
		var q = dojo.objectToQuery(args);
		q = q ? "?" + q: "";
		var headers = options || {};
		headers.Accept = "application/javascript, application/json";
		return dojo.xhrGet({
			url: WEBSITE_URL+'api/'+this.model+'/get.json'+(q || ''),
			handleAs: "json",
			headers: headers
		});
	},
	getIdentity: function(object){
		// summary:
		//		Returns an object's identity
		// object: Object
		//		The object to get the identity from
		//	returns: Number
		return object[this.idProperty];
	},
	put: function(object, options){
		// summary:
		//		Stores an object. This will trigger a PUT request to the server
		//		if the object has an id, otherwise it will trigger a POST request.
		// object: Object
		//		The object to store.
		// options: dojo.store.api.Store.PutDirectives?
		//		Additional metadata for storing the data.
		//	returns: Number
		var data = {};
		for (var k in object) data[this.model+'['+k+']'] = object[k];
		options = options || {};
		data['action['+this.model+']'] = this.post_action;
		return dojo.xhrPost({
				url: WEBSITE_URL+'api/'+this.model+'/get.json',
				content: data,
				handleAs: "json"
			});
	},
	add: function(object, options){
		// summary:
		//		Adds an object. This will trigger a PUT request to the server
		//		if the object has an id, otherwise it will trigger a POST request.
		// object: Object
		//		The object to store.
		// options: dojo.store.api.Store.PutDirectives?
		//		Additional metadata for storing the data.  Includes an "id"
		//		property if a specific id is to be used.
		options = options || {};
		options.overwrite = false;
		return this.put(object, options);
	},
	remove: function(id){
		// summary:
		//		Deletes an object by its identity. This will trigger a DELETE request to the server.
		// id: Number
		//		The identity to use to delete the object
		var args = {};
		args['action['+this.model+']'] = 'delete';
		args[this.model+'[id]'] = id;
		return dojo.xhrPost({
			url: WEBSITE_URL+'api/'+this.model+'/get.json',
			content: args
		});
	},
	query: function(query, options){
		// summary:
		//		Queries the store for objects. This will trigger a GET request to the server, with the
		//		query added as a query string.
		// query: Object
		//		The query to use for retrieving objects from the store.
		// options: dojo.store.api.Store.QueryOptions?
		//		The optional arguments to apply to the resultset.
		//	returns: dojo.store.api.Store.QueryResults
		//		The results of the query, extended with iterative methods.
		var headers = {Accept: "application/javascript, application/json"};
		query = query || {};
		for (x in this.params) if (typeof query[x] == 'undefined') query[x] = this.params[x];
		options = options || {};
		if(options.start >= 0 || options.count >= 0){
			headers.Range = "items=" + (options.start || '0') + '-' +
				(("count" in options && options.count != Infinity) ?
					(options.count + (options.start || 0) - 1) : '');
		}
		query = dojo.objectToQuery(query);
		query = query ? "?" + query: "";
		if(options && options.sort){
			query += (query ? "&" : "?") + "orderby=";
			for(var i = 0; i<options.sort.length; i++){
				var sort = options.sort[i];
				query += (i > 0 ? "," : "") + encodeURIComponent(sort.attribute+' '+(sort.descending ? 'DESC' : 'ASC'));
			}
		}
		var query_url = WEBSITE_URL+'api/'+this.model+'/'+this.action+'.json' + (query || "");
		this.last_query = query_url;
		var results = dojo.xhrGet({
			url: query_url,
			handleAs: "json",
			headers: headers
		});
		results.total = results.then(function(){
			var range = results.ioArgs.xhr.getResponseHeader("Content-Range");
			return range && (range=range.match(/\/(.*)/)) && +range[1];
		});
		return QueryResults(results);
	}
});
});
