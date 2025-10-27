define([
	'dojo/request',
	'dojo/when',
	"dojo/_base/config",
	'dojo/_base/lang',
	'dojo/_base/array',
	'dojo/json',
	'dojo/_base/declare',
	'dstore/Request', /*=====, './Store' =====*/
	'dstore/QueryResults',
	'dstore/Tree',
	'dstore/SimpleQuery',
	'dstore/Trackable'
], function (request, when, config, lang, arrayUtil, JSON, declare, Request /*=====, Store =====*/, QueryResults, Tree, SimpleQuery, Trackable) {

	/*=====
	var __HeaderOptions = {
			// headers: Object?
			//		Additional headers to send along with the request.
		},
		__PutDirectives = declare(Store.PutDirectives, __HeaderOptions),
	=====*/

	var Api = declare([Request, Tree], {

		// stringify: Function
		//		This function performs the serialization of the data for requests to the server. This
		//		defaults to JSON, but other formats can be serialized by providing an alternate
		//		stringify function. If you do want to use an alternate format, you will probably
		//		want to use an alternate parse function for the parsing of data as well.
		stringify: JSON.stringify,
		sortParam:'sort',
		useRangeHeaders:true,

		model: '',
		action:'admin',
		put_action: 'create',
		remove_action:'delete',
		last_query:'',
		results:null,

		constructor: function(options) {
			this.target = config.websiteUrl+'api/'+this.model.replace(/_/g, "-")+'/'+this.action+'.json';
		},

		get: function (id, options) {
			// summary:
			//		Retrieves an object by its identity. This will trigger a GET request to the server using
			//		the url `this.target + id`.
			// id: Number
			//		The identity to use to lookup the object
			// options: Object?
			//		HTTP headers. For consistency with other methods, if a `headers` key exists on this
			//		object, it will be used to provide HTTP headers instead.
			// returns: Object
			//		The object in the store that matches the given id.
			options = options || {};
			var headers = lang.mixin({ Accept: this.accepts }, this.headers, options.headers || options);
			var store = this;
			this.results = request(this.target+'?id=' + id, {
				headers: headers
			}).then(function (response) {
				return store._restore(store.parse(response), true);
			}, lang.hitch(this, 'handleError'));
			return this.results;
		},

		autoEmitEvents: false, // this is handled by the methods themselves

		put: function (object, options) {
			// summary:
			//		Stores an object. This will trigger a PUT request to the server
			//		if the object has an id, otherwise it will trigger a POST request.
			// object: Object
			//		The object to store.
			// options: __PutDirectives?
			//		Additional metadata for storing the data.  Includes an 'id'
			//		property if a specific id is to be used.
			// returns: dojo/_base/Deferred

			options = options || {};
			var store = this;
			var model = options.model || this.model;
			var data = {};
			if (options.formData) data = options.formData;
			else {
				data = object;
				data.oid = config.csrfToken;
			}
			if (options.action) {
				data['action['+model+']'] = options.action;
			}

			var initialResponse = request.post(this._renderUrl(), {
				data: data,
				headers: lang.mixin({
					Accept: this.accepts,
					'If-Match': options.overwrite === true ? '*' : null,
					'If-None-Match': options.overwrite === false ? '*' : null
				}, this.headers, options.headers)
			});
			var finalResponse = initialResponse.then(function (response) {
				var event = {};

				var result = event.target = store._restore(store.parse(response), true) || object;

				when(initialResponse.response, function (httpResponse) {
					store.emit(httpResponse.status === 201 ? 'add' : 'update', event);
				});

				return result;
			}, lang.hitch(this, 'handleError'));

			this.results = initialResponse;
			return finalResponse;
		},

		add: function (object, options) {
			// summary:
			//		Adds an object. This will trigger a PUT request to the server
			//		if the object has an id, otherwise it will trigger a POST request.
			// object: Object
			//		The object to store.
			// options: __PutDirectives?
			//		Additional metadata for storing the data.  Includes an 'id'
			//		property if a specific id is to be used.
			options = options || {};
			options.overwrite = false;
			return this.put(object, options);
		},

		remove: function (id, options) {
			// summary:
			//		Deletes an object by its identity. This will trigger a DELETE request to the server.
			// id: Number
			//		The identity to use to delete the object
			// options: __HeaderOptions?
			//		HTTP headers.

			options = options || {};
			options.onError = options.onError || lang.hitch(this, "handleError");
			var store = this;
			var args;
			if (typeof id == "object") {
				args = id;
			} else {
				args = {id: id};
			}
			args.oid = config.csrfToken;

			this.results = request(this._renderUrl(), {
				method: 'DELETE',
				data: args,
				headers: lang.mixin({}, this.headers, options.headers)
			}).then(function (response) {
				var target = response && store.parse(response);
				store.emit('delete', {id: id, target: target});
				return response ? target : true;
			}, options.onError);
			return this.results;
		},
		fetch: function (kwArgs) {
			var results = this._request(kwArgs);
			this.results = new QueryResults(results.data, {
				response: results.response
			});
			return this.results;
		},
		fetchRange: function (kwArgs) {
			var start = kwArgs.start,
				end = kwArgs.end,
				requestArgs = {};
			if (this.useRangeHeaders) {
				requestArgs.headers = lang.mixin(this._renderRangeHeaders(start, end), kwArgs.headers);
			} else {
				requestArgs.queryParams = this._renderRangeParams(start, end);
				if (kwArgs.headers) {
					requestArgs.headers = kwArgs.headers;
				}
			}

			var results = this._request(requestArgs);
			this.results = new QueryResults(results.data, {
				totalLength: results.total,
				response: results.response
			});
			return this.results;
		},
		_renderFilterParams: function (filter) {
			// summary:
			//		Constructs filter-related params to be inserted into the query string
			// returns: String
			//		Filter-related params to be inserted in the query string
			var type = filter.type;
			var args = filter.args;
			if (!type) {
				return [''];
			}
			if (type === 'string') {
				return [args[0]];
			}
			if (type === 'and' || type === 'or') {
				return [arrayUtil.map(filter.args, function (arg) {
					// render each of the arguments to and or or, then combine by the right operator
					var renderedArg = this._renderFilterParams(arg);
					return ((arg.type === 'and' || arg.type === 'or') && arg.type !== type) ?
						// need to observe precedence in the case of changing combination operators
						'(' + renderedArg + ')' : renderedArg;
				}, this).join(type === 'and' ? '&' : '|')];
			}
			var target = args[1];
			if (target) {
				if(target._renderUrl) {
					// detected nested query, and render the url inside as an argument
					target = '(' + target._renderUrl() + ')';
				} else if (target instanceof Array) {
					target = String(target);
				}
			}
			return [encodeURIComponent(args[0]) + '=' + (type === 'eq' ? '' : type + '=') + encodeURIComponent(target)];
		},
		_renderRangeParams: function (start, end) {
			// summary:
			//		Constructs range-related params to be inserted in the query string
			// returns: String
			//		Range-related params to be inserted in the query string
			var params = [];
			if (this.rangeStartParam) {
				params.push(
					this.rangeStartParam + '=' + start,
					this.rangeCountParam + '=' + (end - start)
				);
			} else {
				params.push('limit=' + (end - start));
				if (start) params.push('skip=' + start);
			}
			return params;
		},
		_renderSortParams: function (sort) {
			// summary:
			//		Constructs sort-related params to be inserted in the query string
			// returns: String
			//		Sort-related params to be inserted in the query string

			var sortString = arrayUtil.map(sort, function (sortOption) {
				var suffix = sortOption.descending ? 'DESC' : 'ASC';
				return encodeURIComponent(sortOption.property+' '+suffix);
			}, this);

			var params = [];
			if (sortString) {
				params.push(this.sortParam ? encodeURIComponent(this.sortParam) + '=' + sortString : 'sort(' + sortString + ')'
				);
			}
			return params;
		},
		mayHaveChildren: function(object) {
			return 'children' in object ? object.children > 0 : true;
		},
		handleError: function(error) {
			var self = this;
			if (error.response.status == 403) {
				var d = new dialog({url:WEBSITE_URL+'forbidden', crudSuffixes:false, callback:function() {
					/*
						var method = (typeof ioargs.args['content'] == "undefined") ? dojo.xhrGet : dojo.xhrPost;
						method(ioargs.args).then(function(results) {
							self.results.resolve(results);
						});
					*/
				}});
				d.show();
			} else if (error.response.status == 500 && error.response.text.substr(0, 1) == '{') {
				var data = JSON.parse(error.response.text);
				var message = 'Message: '+data.message+'\n\nFile: '+data.file+'\n\nLine: '+data.line;
				alert(message);
			}	else if (error.response.status > 0) {
				alert('An unknown error occurred.');
			}
		}
	});

	return declare([Api, Trackable]);

});
