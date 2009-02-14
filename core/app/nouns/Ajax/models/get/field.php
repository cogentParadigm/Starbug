<?php
	function rmloc(&$arr, &$locarr) {
		if (($pos = current($locarr)) !== false) {
			if (next($locarr) === false) {
				$rem = $arr[$pos];
				unset($arr[$pos]);
				return $rem;
			} else rmloc($arr[$pos], $locarr);
		}
	}
	if (!empty($_POST['new_field'])) {
		$filename = "core/db/schema/".$_POST['new_field'];
		$fields = unserialize(file_get_contents($filename));
		if (!isset($fields[$_POST['fieldname']])) $fields[$_POST['fieldname']] = array();
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
?>
<dt id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-key" class="sub">
	<a href="" class="right" onclick="if (confirm('Are you sure you want to delete?')) {delete_key('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return false;}">delete</a>
	<a href="" class="right" onclick="edit_field('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return fals;">rename</a>
	<a href="" class="right" onclick="new_key('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return false;">add key</a>
	<a href="" onclick="showhide('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-fields');return false;"><?php echo $_POST['fieldname']; ?></a>
</dt>
<dd id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>">
	<dl id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-fields">
	</dl>
</dd>
<?php } else {
	$loc = next($this->uri);
	$parts = split("-", $loc, 2);
	$filename = "core/db/schema/".$parts[0];
	$arr = split("-", $parts[1]);
	$fields = unserialize(file_get_contents($filename));
	if ($_POST['edit_field']) {
		$out = rmloc($fields, $arr);
		$merge = array($_POST['key'] => $out);
		end($arr);
		while(($prev = prev($arr)) !== false) $merge = array($prev => $merge);
		$fields = array_merge_recursive($fields, $merge);
		$file = fopen($filename, "wb");
		fwrite($file, serialize($fields));
		fclose($file);
		reset($arr);
	}
	$k = ($_POST['edit_field']) ? $_POST['key'] : end($arr);
	array_pop($arr);
	$keys = "";
	foreach($arr as $key) $keys .= $key."-";
	$keys .= $k;
?>
	<a href="" class="right" onclick="if (confirm('Are you sure you want to delete?')) {delete_key('<?php echo $keys; ?>');return false;}">delete</a>
	<a href="" class="right" onclick="edit_field('<?php echo $keys; ?>');return false;">rename</a>
	<a href="" class="right" onclick="new_key('<?php echo $keys; ?>');return false;">add key</a>
	<a href="" onclick="showhide('<?php echo $keys; ?>-fields');return false;"><?php echo $k; ?></a>
<?php } ?>