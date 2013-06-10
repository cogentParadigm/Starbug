define(["dojo/_base/Deferred", "starbug/grid/Grid", "dgrid/extensions/DnD"], function(Deferred, Grid, DnD){
	var DnDGrid = dojo.declare('starbug.grid.DnDGrid', [Grid, DnD], {
		orderColumn:'position',
		dndParams:{
			withHandles:true,
			onDropInternal: function(nodes, copy, targetItem) {
				
				var store = this.grid.store, grid = this.grid, targetRow, targetPosition;

				if (!this._targetAnchor) return
				
				targetRow = grid.row(this._targetAnchor);
				targetPosition = parseInt(targetRow.data[grid.orderColumn]);
				responses = 1;
				
				nodes.forEach(function(node, idx){
					targetPosition += idx;
					var object = {id:grid.row(node).id};
					object[grid.orderColumn] = targetPosition;
					store.put(object).then(function() {
							if (responses == nodes.length) grid.refresh();
							else responses++;
					});
				});
			
			}
		}
	});
	return DnDGrid;
});

