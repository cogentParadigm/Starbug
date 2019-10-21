define([
	'dojo/_base/declare',
	'dgrid/_StoreMixin',
	"dojo/on",
	"put-selector/put"
], function (declare, _StoreMixin, on, put) {
	return declare(_StoreMixin, {
		// summary:
		// dgrid mixin which implements the refresh method to
		// always perform a single query with no start or count
		// specified, to retrieve all relevant results at once.
		// Appropriate for grids using memory stores with small
		// result set sizes.

		refresh: function () {
			var self = this;

			// First defer to List#refresh to clear the grid's
			// previous content
			this.inherited(arguments);

			if (!this._renderedCollection) {
				return;
			}

			return this._trackError(function () {
				var queryResults = self._renderedCollection.fetch();
				queryResults.totalLength.then(function (total) {
					// Record total so it can be retrieved later via get('total')
					self._total = total;
				});
				return self.renderQueryResults(queryResults).then(function() {
					on.emit(self.domNode, 'dgrid-refresh-complete', {
						bubbles: true,
						cancelable: false,
						grid: self
					});
				});
			});
		},

		renderArray: function () {
			var rows = this.inherited(arguments);

			// Clear _lastCollection which is ordinarily only used for
			// store-less grids
			this._lastCollection = null;

			if (rows.length == 0) {
				this.noDataNode = put(this.contentNode, 'div.dgrid-no-data');
				this.noDataNode.innerHTML = this.noDataMessage;
			}

			return rows;
		}
	});
});
