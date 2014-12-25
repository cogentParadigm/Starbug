define(["dojo/_base/Deferred", "starbug/grid/Grid", "dgrid/extensions/DijitRegistry", "dgrid/extensions/DnD"], function(Deferred, Grid, DijitRegistry, DnD){
	var DnDGrid = dojo.declare('starbug.grid.MemoryGrid', [Grid, DijitRegistry, DnD], {
		orderColumn:'position',
		dndParams:{
			withHandles:true,
			onDropInternal: function(nodes, copy, targetItem) {

					var grid = this.grid, targetRow, targetPosition, fromRow, fromPosition;

					if (!this._targetAnchor) return

					//get target position
					targetRow = grid.row(this._targetAnchor);
					targetPosition = grid.collection.data.indexOf(targetRow.data);

					//get source position
					fromRow = grid.row(nodes[0]);
					fromPosition = grid.collection.data.indexOf(fromRow.data);

					console.log(targetPosition);
					console.log(fromPosition);
					console.log(nodes.length);

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
	return DnDGrid;
});
