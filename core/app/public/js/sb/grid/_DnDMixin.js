define(["dojo/_base/declare", "dgrid/extensions/DnD"], function(declare, DnD){
	return declare([DnD], {
		addUiClasses: false,
		orderColumn:'position',
		orderParams:{},
		dndParams:{
			withHandles:true,
			allowNested:true,
			onDropInternal: function(nodes, copy, targetItem) {
				if (Array.isArray(this.grid.collection.data)) return this.onDropInternalMemory(nodes, copy, targetItem);

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

			},
			onDropInternalMemory: function(nodes, copy, targetItem) {

				var grid = this.grid, targetRow, targetPosition, fromRow, fromPosition;

				if (!this._targetAnchor) return;

				//get target position
				targetRow = grid.row(this._targetAnchor);
				targetPosition = grid.collection.data.indexOf(targetRow.data);

				//get source position
				fromRow = grid.row(nodes[0]);
				fromPosition = grid.collection.data.indexOf(fromRow.data);

				//pull out the movers
				var movers = grid.collection.data.splice(fromPosition, nodes.length);

				//put them back at the new position
				movers.unshift(0);
				if (targetPosition > fromPosition) {
					movers.unshift(targetPosition + 1 - nodes.length);
				} else {
					movers.unshift(targetPosition);
				}
				grid.collection.data.splice.apply(grid.collection.data, movers);
				grid.refresh();

				if (grid.editor) grid.editor.refresh();

			}
		}
	});
});
