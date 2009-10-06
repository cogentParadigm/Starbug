<?php
	$loc = next($this->uri);
	$parts = split("-", $loc, 2);
	$arr = split("-", $parts[1]);
	$_POST['key'] = end($arr);
?>
	<a class="right cancel_edit_field" href="#">cancel</a>
	<a class="right save_edit_field" href="#">save</a>
<form id="edit_field_form" method="post">
	<input name="edit_field" type="hidden" value="<?php echo $loc; ?>" />
	<input id="key" name="key" type="text"<?php if (!empty($_POST['key'])) { ?> value="<?php echo $_POST['key']; ?>"<?php } ?> />
</form>
