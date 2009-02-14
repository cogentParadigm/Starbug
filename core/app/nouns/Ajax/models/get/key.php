<?php
	if (!empty($_POST['add_key'])) {
		$parts = split("-", $_POST['add_key'], 2);
		$filename = "core/db/schema/".$parts[0];
		$arr = split("-", $parts[1]);
		$merge = array(end($arr) => array($_POST['key'] => $_POST['value']));
		while (($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		$fields = unserialize(file_get_contents($filename));
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file) ?>
	<dt><?php echo $_POST['key']; ?></dt><dd id="<?php echo $_POST['add_key']."-".$_POST['key']; ?>"><span class="options"><a href="">edit</a><a href="">delete</a></span><?php echo $_POST['value']; ?></dd>
<?php } else {
	function rmloc(&$arr, &$locarr) {
		if (($pos = current($locarr)) !== false) {
			if (next($locarr) === false) unset($arr[$pos]);
			else rmloc($arr[$pos], $locarr);
		}
	}
	$loc = next($this->uri);
	$parts = split("-", $loc, 2);
	$filename = "core/db/schema/".$parts[0];
	$arr = split("-", $parts[1]);
	$fields = unserialize(file_get_contents($filename));
	if ($_POST['edit_key']) {
		$merge = array(end($arr) => $_POST['value']);
		while(($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		reset($arr);
		rmloc($fields, $arr);
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
		reset($arr);
	}
	$val = $fields[current($arr)];
	while (($k = next($arr)) !== false) $val = $val[$k];
?>
<span class="options">
<a href="" onclick="edit_key('<?php echo $loc; ?>');return false;">edit</a><a href="">delete</a></span><?php echo $val; ?>
</span>
<?php } ?>