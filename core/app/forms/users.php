<?php
	render_display("form", "users", "form", array("action" => "create"));
	efault($action, "create");
?>
<?php if (success("users", $action)) { ?>
	<div class="alert alert-success">Profile updated successfully</div>
<?php } ?>
	<?php
		open_form("model:users  action:$action", "class:users-form");
	?>
	<div class="row">
		<div class="col-md-6">
			<h2>User Information</h2>
			<?php text("first_name"); ?>
			<?php text("last_name"); ?>
		</div>
		<div class="col-md-6">
			<h2>Login Credentials</h2>
			<?php text("email"); ?>
			<?php password("password"); ?>
			<?php password("password_confirm"); ?>
			<?php multiple_category_select("groups  taxonomy:groups"); ?>
		</div>
	</div>
	<div class="btn-group"><button class="submit btn btn-success" type="submit">Save</button><button type="button" class="cancel btn btn-danger" onclick="window.location='<?= uri("admin/users"); ?>'">Cancel</button></div>
	<?php close_form(); ?>	
	<br class="clear"/>
