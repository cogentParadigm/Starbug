<?php $loc = next($this->uri); ?>
<dt id="new-field" class="sub inactive">
<a class="right save_new_field" href="">save</a><a class="right cancel_new_field" href="">cancel</a>
<form id="new_field_form" method="post">
	<input name="new_field" type="hidden" value="<?php echo $loc; ?>" />
	<div style="padding:5px 0px">
		<input id="fieldname" name="fieldname" type="text"<?php if (!empty($_POST['fieldname'])) { ?> value="<?php echo $_POST['fieldname']; ?>"<?php } ?> />
	</div>
</form>
</dt>
