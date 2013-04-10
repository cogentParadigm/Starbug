<?php
	efault($action, "create");
?>
<?php if (success("users", $action)) { ?>
	<div class="success">Profile updated successfully</div>
<?php } ?>
	<?php
		open_form("model:users  action:$action", "class:users-form");
	?>
	<div class="left" style="width:300px;min-height:1px">
		<h2 class="well" style="margin-top:0">Login Credentials</h2>
		<?php text("email"); ?>
		<?php password("password"); ?>
		<?php password("password_confirm"); ?>
		<?php text("memberships"); ?>
	</div>
	<div style="margin-left:320px">
		<h2 class="well">User Information</h2>
		<?php text("first_name"); ?>
		<?php text("last_name"); ?>
	</div>
	<?php /*
		<?php text("address"); ?>
		<?php text("address2"); ?>
		<?php text("city"); ?>
		<?php text("state"); ?>
		<?php text("country"); ?>
		<?php text("zip"); ?>
		*/
		?>
		<div class="clearfix"></div>
	<div class="btn-group"><button class="submit btn" type="submit">Save</button><button type="button" class="cancel btn" onclick="window.location='<?= uri("admin/menus/menu/".$menu); ?>'">Cancel</button></div>
	<?php close_form(); ?>	
	<br class="clear"/>
