define([
  "dojo",
  "dojo/_base/config",
  "dojo/string",
  "put-selector/put",
  "dojo/on"
], function(dojo, config, strings, put, on){
  dojo.global.starbug.grid.columns = dojo.global.starbug.grid.columns || {};
  dojo.global.starbug.grid.columns.options = function(column){

    column.sortable = false;

    if (typeof column.canView == "undefined") column.canView = false;
    if (typeof column.canEdit == "undefined") column.canEdit = true;
    if (typeof column.canCopy == "undefined") column.canCopy = false;
    if (typeof column.canDelete == "undefined") column.canDelete = true;

    column.renderHeaderCell = function(node) {
      if (column.columnStyle) {
        put(node, "[style=" + column.columnStyle + "]");
      }
    };

    var attachButtonHandler = function(button, grid, object) {
      on(button.node, "click", function() {
        var response = button.handler(grid, object);
        if (response && typeof response.then != "undefined") {
          response.then(function(results) {
            grid.refresh();
            if (results.errors) {
              alert(results.errors[0].errors[0]);
            }
          });
        }
      });
    };

    column.renderCell = function(object, value, cell, options, header){
      var grid = this.grid, row = object && grid.row(object), parent = cell.parentNode;
      var base_url = grid.base_url || dojo.global.location.pathname.substr(config.websiteUrl.length);
      base_url = config.websiteUrl + base_url;
      put(parent && parent.contents ? parent : cell, ".dgrid-options");
      if (column.columnStyle) {
        put(cell, "[style=" + column.columnStyle + "]");
      }

      var div = put(cell, "div.dropdown.relative");

      put(div, "button.btn.btn-link[type=button][data-toggle=dropdown][aria-haspopup=true][aria-expanded=false] span.material-icons.v-mid[style=line-height:18px] $", "more_horiz");
      var dropdown = put(div, "ul.dropdown-menu.list.ma0.pl0.tl.bg-white.br2.shadow-4[role=menu]");

      //view button
      if (column.canView) {
        column.viewUrl = column.viewUrl || base_url + "/view/${id}";
        var view = put(dropdown, "li a.db.link.btn[title=View][href="+strings.substitute(column.viewUrl, row)+"] span.fa.fa-desktop.mr2+ span $<", "View");
        if (column.viewTarget) {
          view.setAttribute("target", column.viewTarget);
        }
      }

      // custom buttons
      column.buttons = column.buttons || [];
      for (var i in column.buttons) {
        var button = column.buttons[i];
        if (typeof button.enabled != "undefined") {
          if (typeof button.enabled == "function") {
            if (!button.enabled(object)) {
              continue;
            }
          } else if (!button.enabled) {
            continue;
          }
        }
        button.node = put(dropdown, "li a.db.link.btn[title=" + button.title + "][href=" + strings.substitute(button.url, row) + "] span.fa." + button.icon + ".mr2+ span $<", button.title);
        if (button.target) {
          button.node.setAttribute("target", button.target);
        }
        if (button.handler) {
          attachButtonHandler(button, grid, object);
        }
      }

      //edit button
      if (column.canEdit) {
        if (typeof grid["dialog"] == "string") column.editUrl = "javascript:"+grid["dialog"]+".show("+row.id+")";
        else if (typeof grid["dialog"] == "undefined") column.editUrl = column.editUrl || base_url+"/update/${id}";
        var edit = put(dropdown, "li a.db.link.btn[title=Edit] span.fa.fa-edit.mr2+ span $<", "Edit");
        edit.setAttribute("href", strings.substitute(column.editUrl, row));
        if (column.editTarget) {
          edit.setAttribute("target", column.editTarget);
        }
        if (typeof grid["dialog"] == "object") {
          on(edit, "click", function(evt) {
            grid.dialog.show(row.id);
          });
        } else if (typeof grid["editor"] != "undefined") {
          on(edit, "click", function(evt) {
            grid.editor.edit(row.id);
            evt.preventDefault();
            return false;
          });
        }
      }

      //copy button
      if (column.canCopy) {
        column.copyUrl = column.copyUrl || base_url + "/create?copy=${id}";
        if (typeof grid.dialog == "string") column.copyUrl = "javascript:"+grid.dialog+".show(false, {copy:"+row.id+"})";
        var copy = put(dropdown, "li a.db.link.btn.brand-navy-blue[title=Copy][href="+strings.substitute(column.copyUrl, row)+"] span.fa.fa-files-o.mr2+ span $<", "Copy");
        if (typeof grid.dialog == "object") {
          on(copy, "click", function(e) {
            e.preventDefault();
            grid.dialog.show(false, {copy:row.id});
          });
        } else if (typeof grid.editor != "undefined") {
          on(copy, "click", function(e) {
            e.preventDefault();
            grid.editor.copy(row.id);
          });
        }
      }

      //delete button
      if (column.canDelete) {
        if (typeof grid["dialog"] == "string") column.deleteUrl = "javascript:"+grid["dialog"]+".show("+row.id+")";
        else if (typeof grid["dialog"] == "undefined") column.deleteUrl = column.deleteUrl || base_url+"/delete/${id}";
        var remove = "javascript:;";
        remove = put(dropdown, "li a.db.link.btn.brand-navy-blue[title=Delete][href="+strings.substitute(column.deleteUrl, row)+"] span.fa.fa-times.mr2+ span $<", "Delete");
        on(remove, "click", function(event) {
          event.preventDefault();
          if (confirm("Are you sure you want to delete this item?")) {
            if (typeof grid["editor"] != "undefined") {
              grid.editor.remove(row.id);
            } else {
              var d = grid.collection.remove(row.id);
              d.then(function(result) {
                if (result.errors) {
                  alert(result.errors[0].errors[0]);
                } else {
                  grid.refresh();
                }
              });
            }
          }
        });
      }
    };

    return column;
  };
});
