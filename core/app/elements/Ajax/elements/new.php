<tr id="new_element">
	<td colspan="5">
		<?php $formid = "new_element_form"; $action = "create"; include("core/app/elements/elements/elements_form.php"); ?>
	</td>
	<td>
		<a class="button" href="#" onclick="dojo.byId('new_element').removeNode();return false;">Cancel</a><a class="button" href="#" onclick="submit_element();return false;">Save</a>
	</td>
</tr>