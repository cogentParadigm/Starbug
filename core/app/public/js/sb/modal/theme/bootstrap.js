define(["sb/css/Theme"], function(Theme) {
  return new Theme({
    selectors: {
      dialog: ".modal.fade[role=dialog][tabindex=-1] div.modal-dialog[role=document]",
      contentNode: "div.modal-content",
      headerNode: "div.modal-header",
      bodyNode: "div.modal-body",
      footerNode: "div.modal-footer",
      titleNode: "h4.modal-title",
      closeButton: "button.close[type=button][data-dismiss=modal][aria-label=Close] span[aria-hidden=true]"
    },
    content: {
      closeButton: {innerHTML: '&times;'}
    }
  });
})