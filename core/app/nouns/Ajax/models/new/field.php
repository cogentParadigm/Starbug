<?php $loc = next($this->uri); ?>
<dt id="new-field" class="sub">
<a class="right" href="" onclick="save_new_field('<?php echo $loc; ?>');return false;">save</a><a class="right" href="" onclick="cancel_new_field('<?php echo $loc; ?>');return false;">cancel</a>
<form id="new_field_form" method="post">
	<input name="new_field" type="hidden" value="<?php echo $loc; ?>" />
	<div style="padding:5px 0px">
		<input id="fieldname" name="fieldname" type="text"<?php if (!empty($_POST['fieldname'])) { ?> value="<?php echo $_POST['fieldname']; ?>"<?php } ?> />
	</div>
</form>
</dt>