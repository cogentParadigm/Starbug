define(["sb/css/Theme"], function(Theme) {
  return new Theme({
    selectors: {
      toggleNode: "span.input-group-btn button[type=button][tabindex=-1].btn.btn-default"
    },
    content: {
      toggleNode: {innerHTML: '<span class="fa fa-caret-down"></span><span class="sr-only">Toggle Dropdown</span>'}
    }
  });
})