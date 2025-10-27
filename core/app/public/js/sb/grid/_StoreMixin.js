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
    loadingMessage: '<div class="flex flex-column items-center pa3">' +
                      '<svg class="loading-circle">' +
                        '<circle class="loading-circle-path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />' +
                      '</svg>' +
                      '<p class="f6">Loading Results</p>' +
                    '</div>',
    renderQuery: function (query, options) {
      var self = this,
      container = (options && options.container) || this.contentNode;

      var loadingNode = put(container, "div");
      loadingNode.innerHTML = this.loadingMessage;

      return this._trackError(function () {
        var results = query(options);
        return self.renderQueryResults(results).then(function() {
          return results.totalLength.then(function(total) {
            // Record total so it can be retrieved later via get('total')
            self._total = total;
            put(loadingNode, "!");
          });
        }).otherwise(function (err) {
          // remove the loadingNode and re-throw
          put(loadingNode, "!");
          //domConstruct.destroy(loadingNode);
          throw err;
        });
      });
    },
    refresh: function () {
      var self = this, fetchResults;

      // First defer to List#refresh to clear the grid's
      // previous content
      this.inherited(arguments);

      if (!this._renderedCollection) {
        return;
      }

      // render the query
      // renderQuery calls _trackError internally
      return this.renderQuery(function (queryOptions) {
        var queryResults = self._renderedCollection.fetch();

        queryResults.then(function (results) {
          fetchResults = results;
        });

        // It is important to return the original QueryResults object, which is a special promise
        // with 'totalLength' and 'forEach' properties on it. Returning a chained promise would
        // lose these properties.
        return queryResults;
      }).then(function () {
        self._emitRefreshComplete();

        return fetchResults;
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
