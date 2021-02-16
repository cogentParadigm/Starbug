define(["sb/css/Theme"], function(Theme) {
  return new Theme({
    selectors: {
      dialog: ".modal.fade[role=dialog][tabindex=-1] div.modal-dialog[role=document]",
      contentNode: "div.modal-content",
      headerNode: "div.modal-header.flex.flex-row-reverse.items-center.justify-between",
      bodyNode: "div",
      footerNode: "div.modal-footer.cf",
      titleNode: "h5.modal-title.ma0.lh-solid.b",
      closeButton: "button.close.input-reset.pointer.fr.f6.lh-solid.pa0[type=button][data-dismiss=modal][aria-label=Close] span.material-icons.f6.fr[aria-hidden=true]"
    },
    content: {
      closeButton: "close"
    }
  });
})