<?php $id = next($this->uri); $_POST['users'] = $this->get("users")->find("*", "id='$id'")->fields(); ?>
<td colspan="5">
	<?php $formid = "edit_user_form"; $action = "create"; include("core/app/elements/users/users_form.php"); ?>
</td>
<td>
	<a class="button" href="#" onclick="cancel_edit('<?php echo $id; ?>');return false;">Cancel</a><a class="button" href="#" onclick="save_edit('<?php echo $id; ?>');return false;">Save</a>
</td>