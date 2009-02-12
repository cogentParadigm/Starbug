<?php
	$loc = next($this->uri);
	$parts = split("-", $loc, 2);
	//$filename = "core/db/schema/".ucwords($parts[0]);
	$arr = split("-", $parts[1]);
	//$fields = unserialize(file_get_contents($filename));
	//$val = $fields[current($arr)];
	//while (($k = next($arr)) !== false) $val = $val[$k];
	$_POST['key'] = end($arr);
?>
	<a class="right" href="#" onclick="cancel_edit_field('<?php echo $loc; ?>');return false;">cancel</a>
	<a class="right" href="#" onclick="save_edit_field('<?php echo $loc; ?>');return false;">save</a>
<form id="edit_field_form" method="post">
	<input name="edit_field" type="hidden" value="<?php echo $loc; ?>" />
	<input id="key" name="key" type="text"<?php if (!empty($_POST['key'])) { ?> value="<?php echo $_POST['key']; ?>"<?php } ?> />
</form>