define(['dojo/_base/lang', 'sb/kernel', 'starbug', 'sb/store/Api'], function(lang, sb, starbug, ApiStore) {
			sb.get = function(model, action) {
				if (!action) action = 'admin';
				if (typeof this.stores[model+'.'+action] != 'undefined') return this.stores[model+'.'+action];
				var store = new ApiStore({model:model, action:action});
				//if (model == "uris") store.post_action = "update";
				this.stores[model+'.'+action] = store;
				return store;
			};
			sb.query = function(model, action, query) {
				if (!query) query = {};
				if (!action) action = 'admin';
				return this.get(model, action).filter(query);
			},
			sb.store = function(model, fields, ops) {
				return this.get(model).put(fields, ops).then(lang.hitch(this, function(data) {
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
