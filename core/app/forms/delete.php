<? open_form("model:$model  action:delete", "class:delete_form  onsubmit:return confirm('Are you sure you want to delete this item?');"); ?>
	<? button('<img src="'.uri("core/app/public/icons/cross.png").'"/>', "class:negative  title:delete"); ?>
<? close_form(); ?>
