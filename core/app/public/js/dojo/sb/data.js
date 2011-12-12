define(['dojo', 'sb/kernel', 'starbug', 'starbug/store/Api'], function(dojo, sb, starbug) {
			sb.get = function(models) {
				if (typeof this.stores[models] != 'undefined') return this.stores[models];
				this.stores[models] = new starbug.store.Api({apiQuery:models});
				return this.stores[models];
			};
			sb.query = function(models, query) {
				if (!query) query = {};
				if (typeof query == 'string') query = this.star(query);
				return this.get(models).query(query);
			},
			sb.store = function(model, fields) {
				return this.get(model).put(fields).then(dojo.hitch(this, function(data) {
					if (data.errors) {
						if (typeof this.errors[model] == 'undefined') this.errors[model] = {};
						for (var field in data.errors) {
							field = data.errors[field];
							this.errors[model][field.field] = field.errors;
						}
					}
				}));
			}
			return sb;
});
