define(["dojo/query", "dojo/on"], function(query, on) {
	var handle = function(evt) {
		var action = this.getAttribute("data-action");
		require(["storm/actions/"+action], function(callback) {
			callback();
		});
	};
	on(window.document.body, "[data-action]:click", handle);
});
