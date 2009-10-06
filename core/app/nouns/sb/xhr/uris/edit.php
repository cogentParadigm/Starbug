<?php $id = next($this->uri); $_POST['uris'] = $sb->query("uris", "action:read	where:id='$id'"); ?>
<td colspan="6">
	<?php $formid = "edit_uri_form"; $action = "create"; include("core/app/nouns/sb/uris/uris_form.php"); ?>
</td>
<td>
	<a class="button" href="#" onclick="cancel_edit('<?php echo $id; ?>');return false;">Cancel</a><a class="button" href="#" onclick="save_edit('<?php echo $id; ?>');return false;">Save</a>
</td>
