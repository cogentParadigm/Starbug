<?php $id = next($this->uri); $_POST['elements'] = $this->get_object("Elements")->find("*", "id='$id'"); ?>
<td colspan="5">
	<?php $formid = "update_element_form"; $action = "create"; include("core/app/elements/elements/elements_form.php"); ?>
</td>
<td>
	<a class="button" href="#" onclick="cancel_update();return false;">Cancel</a><a class="button" href="#" onclick="submit_element();return false;">Save</a>
</td>