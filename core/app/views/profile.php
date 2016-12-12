<?php
	if (empty($action)) $action = "update_profile";
	if (empty($form_header) && !empty($model)) {
		$form_header = 'Update Profile';
	}
?>
<div class="panel panel-default">
	<div class="panel-heading"><strong> <span data-i18n="New <?php echo $form_header; ?>"><?php echo $form_header; ?></span></strong></div>
	<div class="panel-body">
		<?php if ($this->db->success("users", "update_profile")) { ?>
			<div class="alert alert-success">You profile has been updated successfully.</div>
		<?php } else { ?>
			<?php $this->displays->render("ProfileForm", array_merge($this->request->getParameters(), array("operation" => $action, "id" => $id))); ?>
		<?php } ?>
	</div>
</div>
