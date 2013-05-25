define(["dojo", "sb", "put-selector/put"],
function(dojo, sb, put){
	dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
	dojo.global.starbug.grid.columns.filesize = function(column){
		
    function formatSize(bytes) {
        if (bytes >= 1073741824) bytes = Math.round((bytes / 1073741824)*100)/100 + ' GB';
        else if (bytes >= 1048576) bytes = Math.round((bytes / 1048576)*100)/100 + ' MB';
        else if (bytes >= 1024) bytes = Math.round((bytes / 1024)*100)/100 + ' KB';
        else if (bytes > 1) bytes = bytes + ' bytes';
        else if (bytes == 1) bytes = bytes + ' byte';
        else bytes = '0 bytes';
        return bytes;
		}

		//populate the cell with the label or value
		column.renderCell = function(object, value, cell, options){
			put(parent && parent.contents ? parent : cell, ".dgrid-filesize");
			cell.innerHTML = formatSize(object.size);
		};
				
		return column;

	};
});
