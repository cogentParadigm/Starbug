<?php $id = next($this->uri); $_POST['elements'] = $this->get("elements")->find("*", "id='$id'")->fields(); ?>
<td colspan="5">
	<?php $formid = "edit_element_form"; $action = "create"; include("core/app/elements/elements/elements_form.php"); ?>
</td>
<td>
	<a class="button" href="#" onclick="cancel_edit('<?php echo $id; ?>');return false;">Cancel</a><a class="button" href="#" onclick="save_edit('<?php echo $id; ?>');return false;">Save</a>
</td>