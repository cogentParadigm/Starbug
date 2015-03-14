<?php open_form("model:$model  action:delete", "class:delete_form  onsubmit:return confirm('Are you sure you want to delete this item?');"); ?>
	<?php button('<div class="sprite icon"></div>', "class:Delete"); ?>
<?php close_form(); ?>
