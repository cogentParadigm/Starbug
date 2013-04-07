define(['dojo', 'sb/kernel', 'starbug', 'starbug/store/Api'], function(dojo, sb, starbug, ApiStore) {
			sb.get = function(model, action) {
				if (!action) action = 'admin';
				if (typeof this.stores[model+'.'+action] != 'undefined') return this.stores[model+'.'+action];
				var store = new ApiStore({model:model, action:action});
				this.stores[model+'.'+action] = store;
				return store;
			};
			sb.query = function(model, action, query) {
				if (!query) query = {};
				if (!action) action = 'admin';
				if (typeof query == 'string') query = this.star(query);
				return this.get(model, action).query(query);
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
