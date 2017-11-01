define(["dojo/_base/declare", "dgrid/extensions/DnD"], function(declare, DnD){
	return declare([DnD], {
		addUiClasses: false,
		orderColumn:'position',
		orderParams:{},
		dndParams:{
			withHandles:true,
			allowNested:true,
			onDropInternal: function(nodes, copy, targetItem) {
				var store = this.grid.collection, list = this.grid, targetRow, targetPosition;

				if (!this._targetAnchor) return;

				targetRow = list.row(this._targetAnchor);
				targetPosition = parseInt(targetRow.data[list.orderColumn]);
				responses = 1;

				nodes.forEach(function(node, idx){
					targetPosition += idx;
					var object = {id:list.row(node).id};
					for(var i in list.orderParams) {
						object[i] = list.orderParams[i];
					}
					object[list.orderColumn] = targetPosition;
					store.put(object).then(function() {
							if (responses == nodes.length) list.refresh();
							else responses++;
					});
				});

			}
		}
	});
});
