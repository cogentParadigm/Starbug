define(["sb/css/Theme"], function(Theme) {
  return new Theme({
    selectors: {
      toggleNode: "span.input-group-btn button[type=button][tabindex=-1].btn.btn-default"
    },
    content: {
      toggleNode: {innerHTML: '<span class="material-icons">expand_more</span><span class="sr-only">Toggle Dropdown</span>'}
    }
  });
})