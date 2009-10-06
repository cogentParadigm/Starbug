<?php
	$loc = next($this->uri);
	$parts = split("-", $loc, 2);
	$filename = "var/schema/".$parts[0];
	$arr = split("-", $parts[1]);
	$fields = unserialize(file_get_contents($filename));
	$val = $fields[current($arr)];
	while (($k = next($arr)) !== false) $val = $val[$k];
	$_POST['value'] = $val;
?>
<span class="options">
	<a href="#" class="cancel_edit_key">cancel</a>
	<a href="#" class="save_edit_key">save</a>
</span>
<form id="edit_key_form" class="keypair_form" method="post">
	<input name="edit_key" type="hidden" value="<?php echo $loc; ?>" />
	<input id="value" name="value" type="text"<?php if (!empty($_POST['value'])) { ?> value="<?php echo $_POST['value']; ?>"<?php } ?> />
</form>
