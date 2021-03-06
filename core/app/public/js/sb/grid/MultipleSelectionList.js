define([
  "dojo/_base/declare",
  "./List",
  "dgrid/Selection"
], function (declare, List, Selection) {
  return declare([List, Selection], {
    selectionMode: "extended",
    deselectOnRefresh: false,
    allowSelectAll: true
  });
});
