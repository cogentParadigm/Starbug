<?php
	include("core/app/models/Models.php");
	$models = new Models($sb->db);
	if (!empty($_POST['new_field'])) {
		$models->add_field($_POST['fieldname'], $_POST['new_field']);
		$has = $sb->has($_POST['new_field']);
?>
<dt id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-key" class="sub inactive">
	<a href="" class="right" onclick="if (confirm('Are you sure you want to delete?')) {delete_key('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return false;}">delete</a>
	<a href="" class="right" onclick="edit_field('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return fals;">rename</a>
	<a href="" class="right" onclick="new_key('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>');return false;">add key</a>
	<?php if ($has) { ?>
	<form style="display:none" id="activate_<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>">
		<input type="hidden" name="activate_field" value="1" />
	</form>
	<a href="" class="right" onclick="activate_field('<?php echo $_POST['new_field']; ?>', '<?php echo $_POST['fieldname']; ?>');return false;">activate</a>
	<?php } ?>
	<a href="" onclick="showhide('<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-fields');return false;"><?php echo $_POST['fieldname']; ?></a>
</dt>
<dd id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>" class="inactive">
	<dl id="<?php echo $_POST['new_field']."-".$_POST['fieldname']; ?>-fields">
	</dl>
</dd>
<?php } else {
	$loc = next($this->uri);
	$parts = split("-", $loc, 2);
	if ($_POST['edit_field']) {
		$models->edit($_POST['key'], $loc);
		$keys = split("-", $parts[1]);
		array_pop($keys);
		$keys[] = $_POST['key'];
		$parts[1] = join("-", $keys);
	}
	$keys = $parts[1];
	$k = end(split("-", $keys));
?>
	<a href="" class="right" onclick="if (confirm('Are you sure you want to delete?')) {delete_key('<?php echo $keys; ?>');return false;}">delete</a>
	<a href="" class="right" onclick="edit_field('<?php echo $keys; ?>');return false;">rename</a>
	<a href="" class="right" onclick="new_key('<?php echo $keys; ?>');return false;">add key</a>
	<a href="" onclick="showhide('<?php echo $keys; ?>-fields');return false;"><?php echo $k; ?></a>
<?php } ?>
