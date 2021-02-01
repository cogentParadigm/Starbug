define(["dojo", "dojo/_base/config", "sb/strings", "put-selector/put", "dojo/on"],
function(dojo, config, strings, put, on){
  dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
  dojo.global.starbug.grid.columns.import_options = function(column){

    column.sortable = false;

    column.renderCell = function(object, value, cell, options, header){
      var grid = this.grid, row = object && grid.row(object), parent = cell.parentNode;
      var base_url = grid.base_url || dojo.global.location.pathname.substr(config.websiteUrl.length);
      base_url = config.websiteUrl + base_url;
      put(parent && parent.contents ? parent : cell, ".dgrid-options");

      var div = put(cell, 'div.btn-group');

      put(div, 'a.Run.btn.btn-default[title=Run][href='+base_url+'/run/'+row.id+window.location.search+']', put('div.fa.fa-play'));

      //edit button
      var href = "javascript:;";
      if (typeof grid['dialog'] == 'string') href = 'javascript:'+grid['dialog']+'.show('+row.id+')';
      else if (typeof grid['dialog'] == 'undefined') href = base_url+'/update/'+row.id+dojo.global.location.search;
      var edit = put(div, 'a.Edit.btn.btn-default[title=Edit][href='+href+']', put('div.fa.fa-edit'));
      if (typeof grid['dialog'] == "object") {
        on(edit, 'click', function(evt) {
          grid.dialog.show(row.id);
        });
      } else if (typeof grid['editor'] != 'undefined') {
        on(edit, 'click', function(evt) {
          grid.editor.edit(row.id);
          evt.preventDefault();
          return false;
        });
      }

      //copy button
      var chref = base_url + '/create?copy='+row.id;
      if (typeof grid.dialog == 'string') chref = 'javascript:'+grid.dialog+'.show(false, {copy:'+row.id+'})';
      var copy = put(div, 'a.Copy.btn.btn-default[title=Copy][href='+chref+']', put('div.fa.fa-files-o'));
      if (typeof grid.dialog == "object") {
        on(copy, 'click', function(e) {
          e.preventDefault();
          grid.dialog.show(false, {copy:row.id});
        });
      } else if (typeof grid.editor != "undefined") {
        on(copy, 'click', function(e) {
          e.preventDefault();
          grid.editor.copy(row.id);
        });
      }

      //delete button
      var remove = 'javascript:;';
      remove = put(div, 'a.Delete.btn.btn-default[title=Delete][href='+remove+']', put('div.fa.fa-times'));
      on(remove, 'click', function() {
        if (confirm('Are you sure you want to delete this item?')) {
          if (typeof grid['editor'] != "undefined") {
            grid.editor.remove(row.id);
          } else {
            var d = grid.collection.remove(row.id);
            d.then(function() {grid.refresh();});
          }
        }
      });
    };

    return column;
  };
});
