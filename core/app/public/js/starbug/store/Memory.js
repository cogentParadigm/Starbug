define([
	"dojo", "dojo/_base/declare", "dojo/_base/lang", "dojo/_base/Deferred", "dojo/store/util/QueryResults", "dojo/store/Memory"
], function(dojo, declare, lang, Deferred, QueryResults, Memory) {
	return declare("starbug.store.Memory", Memory, {
			query: function(query, options){
				query = query || {};
					if (query.keywords) {
						var keywords = query.keywords;
						query = function(item) {
							var matches = item.label.match(keywords);
							return matches && matches.length;
						};
					}
					var def = new Deferred();
					var immediateResults = this.queryEngine(query, options)(this.data);
					setTimeout(function(){
							def.resolve(immediateResults);
					}, 50);
					var results = QueryResults(def.promise);
					return results;
			},
			put: function(object, options){
				// summary:
				//		Stores an object
				// object: Object
				//		The object to store.
				// options: dojo/store/api/Store.PutDirectives?
				//		Additional metadata for storing the data.  Includes an "id"
				//		property if a specific id is to be used.
				// returns: Number
				var data = this.data,
					index = this.index,
					idProperty = this.idProperty;
				var id = object[idProperty] = (options && "id" in options) ? options.id : idProperty in object ? object[idProperty] : Math.random();
				if(id in index){
					// object exists
					if(options && options.overwrite === false){
						throw new Error("Object already exists");
					}
					// replace the entry in data
					for (var k in object) data[index[id]][k] = object[k];
				}else{
					// add the new object
					index[id] = data.push(object) - 1;
				}

				var def = new Deferred();
				setTimeout(function() {
					def.resolve(object);
				}, 50);
				return def.promise;
			}
	});
});
