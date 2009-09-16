<?php $id = next($this->uri); $_POST['users'] = $sb->get("users")->find("*", "id='$id'")->fields(); ?>
<td colspan="5">
	<?php $formid = "edit_user_form"; $action = "create"; include("core/app/nouns/users/users_form.php"); ?>
</td>
<td>
	<a class="button" href="#" onclick="cancel_edit('<?php echo $id; ?>');return false;">Cancel</a><a class="button" href="#" onclick="save_edit('<?php echo $id; ?>');return false;">Save</a>
</td>
