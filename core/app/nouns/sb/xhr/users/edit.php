<?php $id = next($this->uri); $_POST['users'] = $sb->query("users", "action:read	where:id='$id'"); ?>
<td colspan="5">
	<?php $formid = "edit_user_form"; $action = "create"; include("core/app/nouns/sb/users/users_form.php"); ?>
</td>
<td>
	<a class="button" href="#" onclick="cancel_edit('<?php echo $id; ?>');return false;">Cancel</a><a class="button" href="#" onclick="save_edit('<?php echo $id; ?>');return false;">Save</a>
</td>
