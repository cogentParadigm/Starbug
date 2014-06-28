define([
	"dojo", "dojo/_base/declare", "dojo/_base/lang", "dojo/_base/Deferred", "dojo/store/util/QueryResults", "dojo/store/Memory"
], function(dojo, declare, lang, Deferred, QueryResults, Memory) {
	return declare("starbug.store.Memory", Memory, {
			query: function(query, options){
					var def = new Deferred();
					var immediateResults = this.queryEngine(query, options)(this.data);
					setTimeout(function(){
							def.resolve(immediateResults);
					}, 50);
					var results = QueryResults(def.promise);
					return results;
			}
	});
});
